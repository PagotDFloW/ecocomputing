<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProduitsRepository;
use App\Repository\ServicesRepository;
use App\Repository\UserRepository;
use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Produits;
use App\Entity\Categories;
use App\Entity\Services;
use App\Entity\Avis;
use App\Entity\Promotions;
use App\Entity\Commandes;
use App\Entity\DemandeService;
use App\Entity\ProduitsCommande;
use Faker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/script", name="script_")
 * @IsGranted ("ROLE_ADMIN")
 */
class ScriptController extends AbstractController
{

    //script pour créer des utilisateurs
    /**
     * @Route("/create-users", name="create_users")
     */
    public function createUsers(EntityManagerInterface $manager){
        $fake = Faker\Factory::create('fr_FR');
        for ($i=0; $i < 100; $i++) {
            $user = new User();
            $user->setEmail($fake->email);
            $user->setPassword($fake->password);
            $user->setRoles(['ROLE_USER']);
            $user->setPhone($fake->phoneNumber);
            $user->setNom($fake->name);
            $user->setPrenom($fake->firstName);
            $user->setIsVerified(true);
            $manager->persist($user);
        }
        $manager->flush();
        return new Response('Utilisateurs créés');
    }
    //script pour créer des catégories
    /**
     * @Route("/categories", name="categories")
     */
    public function createCategories(EntityManagerInterface $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $array = ['PC', 'Ordinateur portable', 'Tablette', 'Smartphone', 'Accessoire', 'Autre'];
        for ($i = 0; $i < 10; $i++) {
            $category = new Categories();
            $category->setName($array[rand(0, 5)]);
            $manager->persist($category);
        }
        $manager->flush();
        return new Response('Les catégories ont été créés');
    }

    //script pour créer des produits
    /**
     * @Route("/produits", name="produits")
     */
    public function createProduits(EntityManagerInterface $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $array = ['Mac Book', 'AlienWare', 'Acer Predator', 'Asus', 'Dell', 'HP', 'Lenovo', 'MSI', 'Razer', 'Samsung', 'Sony', 'Toshiba', "RTX 3060","RTX 3070","RTX 2060","RTX 3080", "GTX 1650", 'MSI ECRAN 144 HZ'];
        $urlImages = [
            'https://m.media-amazon.com/images/I/61FIlWIQuQL._AC_SL1500_.jpg',
            'https://m.media-amazon.com/images/I/61aTywrhyBS._AC_SX425_.jpg',
            "https://image.jeuxvideo.com/medias-md/166013/1660125672-3910-card.png",
            "https://m.media-amazon.com/images/I/71WJoedLzPL._AC_SL1500_.jpg",
            "https://www.notebookcheck.biz/uploads/tx_nbc2/71LXTAab-_L._SL1500__03.jpg",
            "https://www.notebookcheck.biz/uploads/tx_nbc2/MSI_GF63_8RD-028ES_01.jpg",
            "https://www.cdiscount.com/pdt2/7/7/8/1/700x700/cor0840006620778/rw/corsair-memoire-pc-ddr4-vengeance-rgb-pro-32gb.jpg",
            "https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/ASRock_K7VT4A_Pro_Mainboard.jpg/640px-ASRock_K7VT4A_Pro_Mainboard.jpg",
            "https://media.materiel.net/r550/oproducts/AR201710030064_g1.jpg",
            "https://m.media-amazon.com/images/I/610JKJVskzL._AC_SS450_.jpg",
            "https://m.media-amazon.com/images/I/71-Q-Q-Q-QL._AC_SS450_.jpg",
            "https://m.media-amazon.com/images/I/71-Q-Q-Q-QL._AC_SS450_.jpg",
        ];

        for ($i = 0; $i < 100; $i++) {
            $produit = new Produits();
            $produit->setName($array[rand(0, count($array) - 1)].'version '.$faker->numberBetween(1, 250));
            $produit->setDescription($faker->text);
            $produit->setPrix($faker->numberBetween(1, 100));
            $produit->setImage1($urlImages[rand(0, count($urlImages) - 1)]);
            $produit->setImage2($urlImages[rand(0, count($urlImages) - 1)]);
            $produit->setImage3($urlImages[rand(0, count($urlImages) - 1)]);
            $produit->setCategorie($this->getDoctrine()->getRepository(Categories::class)->find(rand(1, 12)));
            $produit->setStatut('neuf');
            $produit->setProduitCondition('neuf');
            $produit->setCreatedAt($faker->dateTimeBetween('-1 years', 'now'));
            $manager->persist($produit);
        }
        $manager->flush();
        return new Response('Les produits ont été créés');
    }



    //script pour créer des services
    /**
     * @Route("/services", name="services")
     */
    public function createServices(EntityManagerInterface $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $array= ['Installation Systeme d\'exploitation', 'Maintenance', 'Réparation Ventilation','Réparation Globale', 'Support', 'Autre'];
        for ($i = 0; $i < 10; $i++) {
            $service = new Services();
            $service->setName($array[rand(0, 5)]);
            $service->setDescription($faker->text);
            $service->setPrice($faker->numberBetween(1, 100));
            $manager->persist($service);
        }
        $manager->flush();
        return new Response('Les services ont été créés');
    }
    //script pour créer des demandes de services
    /**
     * @Route("/demandes", name="demandes")
     */
    public function createDemandes(EntityManagerInterface $manager, UserRepository $userRepository, ServicesRepository $servicesRepository)
    {
        $faker = Faker\Factory::create('fr_FR');
        $array = ['Mac Book', 'AlienWare', 'Acer Predator', 'Asus', 'Dell', 'HP', 'Lenovo', 'MSI', 'Razer', 'Samsung', 'Sony', 'Toshiba'];
        for ($i = 0; $i < 100; $i++) {
            $demande = new DemandeService();
            $demande->setClient($userRepository->findOneBy(['id' => rand(10, 100)]));
            $demande->setService($servicesRepository->findOneBy(['id' => rand(1, 10)]));
            $demande->setComputer($array[$faker->numberBetween(0, 9)].'version d\'usine '.$faker->numberBetween(1900, 2500));
            $manager->persist($demande);
        }
        $manager->flush();
        return new Response('Les demandes ont été créés');
    }

    //script pour créer des commandes
    /**
     * @Route("/commandes", name="commandes")
     */
    public function createCommandes(EntityManagerInterface $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $commande = new Commandes();
            $commande->setUser($this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => rand(1, 100)]));
            $commande->setDatetime($faker->dateTimeBetween('-1 years', 'now'));
            $commande->setAdresse($faker->address);
            $commande->setCodePostal(intval($faker->postcode));
            $commande->setVille($faker->city);
            $commande->setTotal($faker->numberBetween(1, 100));
            $commande->setPaiement($faker->creditCardType);
            $commande->setLivraison($faker->creditCardType);
            $commande->setStatus('en cours');
            $manager->persist($commande);
        }
        $manager->flush();
        return new Response('Les commandes ont été créés');
    }

    //script pour créer les produits commandés
    /**
     * @Route("/produits-commandes", name="produits_commandes")
     */
    public function createProduitsCommandes(EntityManagerInterface $manager, CommandesRepository $commandesRepository, ProduitsRepository $produitsRepository)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $produitCommande = new ProduitsCommande();
            $produitCommande->setCommande($commandesRepository->findOneBy(['id' => rand(1, 100)]));
            $produitCommande->setProduit($produitsRepository->findOneBy(['id' => rand(1, 100)]));
            $produitCommande->setQuantity($faker->numberBetween(1, 100));
            $manager->persist($produitCommande);
        }
        $manager->flush();
        return new Response('Les produits commandés ont été créés');
    }

    //script pour créer les promotions
    /**
     * @Route("/promotions", name="promotions")
     */
    public function createPromotions(EntityManagerInterface $manager, ProduitsRepository $produitsRepository, CategoriesRepository $categoriesRepository)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 20; $i++) {
            $promotion = new Promotions();
            $promotion->setProduit($produitsRepository->findOneBy(['id' => rand(1, 100)]));
            $promotion->setCategory($categoriesRepository->findOneBy(['id' => rand(1, 12)]));
            $promotion->setReduction($faker->numberBetween(1, 100));
            $promotion->setStartAt($faker->dateTimeBetween('-1 years', 'now'));
            $promotion->setEndAt($faker->dateTimeBetween('-1 years', 'now'));
            $promotion->setText($faker->text);
            $promotion->setStatut('actif');
            $manager->persist($promotion);
        }
        $manager->flush();
        return new Response('Les promotions ont été créés');
    }

    /**
     * @Route("/generate-data", name="generate_data")
     */
    public function generateData(){
        $this->createUsers($this->getDoctrine()->getManager());
        $this->createCategories($this->getDoctrine()->getManager());
        $this->createProduits($this->getDoctrine()->getManager());
        $this->createServices($this->getDoctrine()->getManager());
        $this->createDemandes($this->getDoctrine()->getManager(), $this->getDoctrine()->getRepository(User::class), $this->getDoctrine()->getRepository(Services::class));
        $this->createCommandes($this->getDoctrine()->getManager());
        $this->createProduitsCommandes($this->getDoctrine()->getManager(), $this->getDoctrine()->getRepository(Commandes::class), $this->getDoctrine()->getRepository(Produits::class));
        $this->createPromotions($this->getDoctrine()->getManager(), $this->getDoctrine()->getRepository(Produits::class), $this->getDoctrine()->getRepository(Categories::class));
        exit('Les données ont été générées');

    }





}
