<?php

namespace App\Tests\Controller\V1;

use App\Entity\Sauce;
use App\Enum\SauceServiceEnum;
use App\Sauce\Css\LoLSauceCss;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SauceControllerTest extends WebTestCase
{
    private function createAuthenticatedClient(): KernelBrowser
    {
        $client = static::createClient();
        $client->request('POST', '/api/v1/auth/anonymous');
        self::assertResponseStatusCodeSame(201);

        return $client;
    }

    public function testSauceEndpointsRequireAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/v1/sauces');
        self::assertResponseStatusCodeSame(401);

        $client->request('GET', '/api/v1/sauce/lol');
        self::assertResponseStatusCodeSame(401);

        $client->request('GET', '/api/v1/sauce/lol/css');
        self::assertResponseStatusCodeSame(401);
    }

    public function testGetSaucesAndGetSauceBySlug(): void
    {
        $client = $this->createAuthenticatedClient();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $slug = 'lol-sauce-'.bin2hex(random_bytes(4));
        $sauce = (new Sauce())
            ->setName('League of Legends Sauce')
            ->setSlug($slug)
            ->setService(SauceServiceEnum::LoLSauce);

        $em->persist($sauce);
        $em->flush();

        // List all sauces
        $client->request('GET', '/api/v1/sauces');
        self::assertResponseIsSuccessful();
        $sauces = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertIsArray($sauces);

        $matching = array_values(array_filter($sauces, static fn (array $item): bool => ($item['slug'] ?? null) === $slug));
        self::assertCount(1, $matching);
        self::assertSame('League of Legends Sauce', $matching[0]['name']);
        self::assertSame(SauceServiceEnum::LoLSauce->value, $matching[0]['service']);

        // Get single sauce by slug
        $client->request('GET', '/api/v1/sauce/'.$slug);
        self::assertResponseIsSuccessful();
        $singleSauce = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertSame($sauce->getId(), $singleSauce['id']);
        self::assertSame('League of Legends Sauce', $singleSauce['name']);
        self::assertSame($slug, $singleSauce['slug']);
    }

    public function testGetSauceCssReturnsExpectedStylesAndHandlesNotFound(): void
    {
        $client = $this->createAuthenticatedClient();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $lolSlug = 'lol-css-'.bin2hex(random_bytes(4));
        $lolSauce = (new Sauce())
            ->setName('LoL Sauce')
            ->setSlug($lolSlug)
            ->setService(SauceServiceEnum::LoLSauce);

        $blankSlug = 'blank-css-'.bin2hex(random_bytes(4));
        $blankSauce = (new Sauce())
            ->setName('Blank Sauce')
            ->setSlug($blankSlug)
            ->setService(SauceServiceEnum::BlankSauce);

        $em->persist($lolSauce);
        $em->persist($blankSauce);
        $em->flush();

        // LoLSauce CSS
        $client->request('GET', '/api/v1/sauce/'.$lolSlug.'/css');
        self::assertResponseIsSuccessful();
        $lolCss = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertSame((new LoLSauceCss())->getCss(), $lolCss);

        // BlankSauce CSS
        $client->request('GET', '/api/v1/sauce/'.$blankSlug.'/css');
        self::assertResponseIsSuccessful();
        $blankCss = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertSame([], $blankCss);

        // Unknown slug -> 404
        $client->request('GET', '/api/v1/sauce/non-existent-slug/css');
        self::assertResponseStatusCodeSame(404);
    }
}
