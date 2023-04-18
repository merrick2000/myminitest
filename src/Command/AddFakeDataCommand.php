<?php

namespace App\Command;

use App\Entity\Product;
use App\Entity\ProductVariant;
use App\Entity\SizeVariant;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddFakeDataCommand extends Command
{
    protected static $defaultName = 'app:generate-fake-data';
    protected static $defaultDescription = 'Generates fake data for testing purposes.';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $faker = Factory::create();
        //Create Users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setPassword(password_hash('password', PASSWORD_DEFAULT));
            $user->setCredits($faker->randomFloat(2, 0, 1000));
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();

        //Generate Size Variants
        // Create an array of unique sizes
        $sizes = ['Small', 'Medium', 'Large', 'Extra Large'];
        shuffle($sizes);
        for ($i = 0; $i < count($sizes); $i++) {
            $sizeVariant = new SizeVariant();
            $sizeVariant->setSize($sizes[$i]);
            $sizeVariant->setCreatedAt($faker->dateTimeThisYear());
            $this->entityManager->persist($sizeVariant);
        }
        $this->entityManager->flush();

        //Create Products
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName($faker->text(rand(8, 15)))
                    ->setDescription($faker->paragraphs(3, true))
                    ->setPrice($faker->randomFloat(2, 10, 100))
                    ->setCreatedAt($faker->dateTimeBetween('-1 year', 'now'));
            $this->entityManager->persist($product);
        }
        $this->entityManager->flush();

        //Create Product Variant
        // Get all products and size variants from database
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        $sizeVariants = $this->entityManager->getRepository(SizeVariant::class)->findAll();

        // Loop through each product
        foreach ($products as $product) {
            // Loop through each size variant
            foreach ($sizeVariants as $sizeVariant) {
                // Create a new product variant
                $productVariant = new ProductVariant();
                
                // Set the product and size variant
                $productVariant->setProduct($product);
                $productVariant->setSizeVariant($sizeVariant);
                $productVariant->setPrice($product->getPrice() + rand(5, 25));
                
                // Set a random created at date within the last year
                $createdAt = new \DateTime();
                $createdAt->modify('-' . rand(0, 364) . ' days');
                $productVariant->setCreatedAt($createdAt);
                
                // Add the product variant to the product's collection of variants
                $product->addProductVariant($productVariant);
                
                // Persist the product variant
                $this->entityManager->persist($productVariant);
            }
        }
        $this->entityManager->flush();
        $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
