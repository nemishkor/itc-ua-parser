<?php
declare(strict_types=1);

namespace App\Tests\Services\Parsers;

use App\Entity\Article;
use App\Services\Parsers\ItcUaParser;
use App\ValueObject\ParseQuery;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ItcUaParserTest extends KernelTestCase {

    public function testParseFromUrl() {
        self::bootKernel();
        /** @var ItcUaParser $parser */
        $parser = self::$container->get(ItcUaParser::class);

        $collection = $parser->parse(new ParseQuery('https://itc.ua/', null, 2, true));
        self::assertEquals(2, $collection->count());

        $firstArticle = $collection->get(0);
        self::assertEquals(Article::class, get_class($firstArticle));
        /** @var Article $firstArticle */
        self::assertNotEmpty($firstArticle->getTitle());
        self::assertNotEmpty($firstArticle->getUrl());
        self::assertNotEmpty($firstArticle->getCreated());
        self::assertNotEmpty($firstArticle->getContent());

        $secondArticle = $collection->get(0);
        self::assertEquals(Article::class, get_class($secondArticle));
        /** @var Article $secondArticle */
        self::assertNotEmpty($secondArticle->getTitle());
        self::assertNotEmpty($secondArticle->getUrl());
        self::assertNotEmpty($secondArticle->getCreated());
        self::assertNotEmpty($secondArticle->getContent());

    }

    public function testParseHomePageListOnly() {

        self::bootKernel();
        /** @var ItcUaParser $parser */
        $parser = self::$container->get(ItcUaParser::class);

        $html = <<<PHP_EOL
<html prefix="og: https://ogp.me/ns#"
      class="js cssanimations wf-roboto-n4-active wf-roboto-i4-active wf-roboto-n7-active wf-active" lang="ru-RU">
<head><meta charset="UTF-8"><title>Home</title></head>
<body data-rsssl="1" class="home blog wp-custom-logo itc-body front-page js">
<div id="container">
    <section id="wrapper">
        <div class="container">
            <div class="row">
                <main id="content" class="col-md-8 col-content">
                    <div class="post block-in-loop post-648985 type-post status-publish format-standard has-post-thumbnail category-news tag-ea tag-ian-rajt">
                        <div class="row">
                            <div class="col-xs-4 col-img">
                                <div class="col-img-in"><span class="cat part text-uppercase"> <a
                                        href="https://itc.ua/category/news" class="itc-cat news a-not-img"
                                        rel="category follow" data-wpel-link="internal"
                                        target="_self">Новости</a> </span> <a
                                        href="https://itc.ua/news/ea-pozhiznenno-zabanila-igroka-fifa-posle-ego-rasistskih-oskorblenij-v-adres-eks-futbolista-iana-rajta/"
                                        class="thumb-responsive lazy-load a-not-img lazy-loaded"
                                        data-wpel-link="internal" target="_self" rel="follow"
                                        style="background-image: url(&quot;https://itc.ua/wp-content/uploads/2021/03/eaianwright-01-450x300.png&quot;);"></a>
                                </div>
                            </div>
                            <div class="col-xs-8 col-txt"><h2 class="entry-title text-uppercase "><a
                                    href="https://itc.ua/news/ea-pozhiznenno-zabanila-igroka-fifa-posle-ego-rasistskih-oskorblenij-v-adres-eks-futbolista-iana-rajta/"
                                    rel="bookmark follow" data-wpel-link="internal" target="_self" class="a-not-img">EA
                                пожизненно забанила игрока FIFA после его расистских оскорблений в адрес экс-футболиста
                                Иана Райта</a></h2>
                                <div class="entry-header">
                                    <div class=""><span class="date part"> <span
                                            class="icon icon-ic-schedule-black-48px"></span> <time class="published"
                                                                                                   datetime="2021-03-04T13:37:00+02:00"> 04.03.2021 в 13:37 </time> <time
                                            class="screen-reader-text updated" datetime="2021-03-04T13:33:12+02:00"> Обновлено: 04.03.2021 в 13:33 </time> </span>
                                        <span class="vcard author part hidden-xs"> <span
                                                class="icon icon-ic-person-black-48px"></span> <a
                                                href="https://itc.ua/author/kolomiets/"
                                                title="Записи Александр Коломиец" rel="author follow"
                                                data-wpel-link="internal" target="_self" class="a-not-img">Александр Коломиец</a> <a
                                                href="https://itc.ua/author/kolomiets/"
                                                class="screen-reader-text fn a-not-img" data-wpel-link="internal"
                                                target="_self" rel="follow">Александр Коломиец</a> </span> <span
                                                class="comments part"> <span
                                                class="icon icon-ic-comment-black-48px"></span> <a
                                                href="https://itc.ua/news/ea-pozhiznenno-zabanila-igroka-fifa-posle-ego-rasistskih-oskorblenij-v-adres-eks-futbolista-iana-rajta/#itc-comments"
                                                class="disqus-comment-count a-not-img" data-disqus-identifier="648985"
                                                data-wpel-link="internal" target="_self" rel="follow"></a> </span></div>
                                </div>
                                <div class="entry-excerpt hidden-xs"> Компания Electronic Arts пожизненно забанила
                                    игрока в FIFA после того, как он проявил расизм по отношению к бывшему футболисту.
                                    Патрик О’Брайен проиграл игру в режиме Ultimate Team с виртуальной версией…
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="post block-in-loop post-649022 type-post status-publish format-standard has-post-thumbnail category-news tag-postmen tag-biblioteki tag-lesya-ukrayinka tag-nova-poshta tag-np-lukash tag-roboti">
                        <div class="row">
                            <div class="col-xs-4 col-img">
                                <div class="col-img-in"><span class="cat part text-uppercase"> <a
                                        href="https://itc.ua/category/news" class="itc-cat news a-not-img"
                                        rel="category follow" data-wpel-link="internal"
                                        target="_self">Новости</a> </span> <a
                                        href="https://itc.ua/news/nova-poshta-i-postmen-vipustili-robota-bibliotekarya-yakij-dopomaga%d1%94-pidibrati-knizhku-vidviduvacham-ekspoziczi%d1%97-lesya-ukra%d1%97nka-150-imen/"
                                        class="thumb-responsive lazy-load a-not-img lazy-loaded"
                                        data-wpel-link="internal" target="_self" rel="follow"
                                        style="background-image: url(&quot;https://itc.ua/wp-content/uploads/2021/03/np-lukash-450x253.jpg&quot;);"></a>
                                </div>
                            </div>
                            <div class="col-xs-8 col-txt"><h2 class="entry-title text-uppercase "><a
                                    href="https://itc.ua/news/nova-poshta-i-postmen-vipustili-robota-bibliotekarya-yakij-dopomaga%d1%94-pidibrati-knizhku-vidviduvacham-ekspoziczi%d1%97-lesya-ukra%d1%97nka-150-imen/"
                                    rel="bookmark follow" data-wpel-link="internal" target="_self" class="a-not-img">Нова
                                пошта і Postmen випустили робота-бібліотекаря «НП Лукаш», який допомагає підібрати
                                книжку відвідувачам експозиції «Леся Українка: 150 імен»</a></h2>
                                <div class="entry-header">
                                    <div class=""><span class="date part"> <span
                                            class="icon icon-ic-schedule-black-48px"></span> <time class="published"
                                                                                                   datetime="2021-03-04T13:18:22+02:00"> 04.03.2021 в 13:18 </time> <time
                                            class="screen-reader-text updated" datetime="2021-03-04T13:20:22+02:00"> Обновлено: 04.03.2021 в 13:20 </time> </span>
                                        <span class="vcard author part hidden-xs"> <span
                                                class="icon icon-ic-person-black-48px"></span> <a
                                                href="https://itc.ua/author/kulesh/" title="Записи Сергей Кулеш"
                                                rel="author follow" data-wpel-link="internal" target="_self"
                                                class="a-not-img">Сергей Кулеш</a> <a
                                                href="https://itc.ua/author/kulesh/"
                                                class="screen-reader-text fn a-not-img" data-wpel-link="internal"
                                                target="_self" rel="follow">Сергей Кулеш</a> </span> <span
                                                class="comments part"> <span
                                                class="icon icon-ic-comment-black-48px"></span> <a
                                                href="https://itc.ua/news/nova-poshta-i-postmen-vipustili-robota-bibliotekarya-yakij-dopomaga%d1%94-pidibrati-knizhku-vidviduvacham-ekspoziczi%d1%97-lesya-ukra%d1%97nka-150-imen/#itc-comments"
                                                class="disqus-comment-count a-not-img" data-disqus-identifier="649022"
                                                data-wpel-link="internal" target="_self" rel="follow"></a> </span></div>
                                </div>
                                <div class="entry-excerpt hidden-xs"> Нова пошта і Postmen випустили
                                    робота-бібліотекаря, який допомагає відвідувачам експозиції «Леся Українка: 150
                                    імен» підібрати книжку. Робот-бібліотекар отримав назву НП Лукаш і вміє розрізняти…
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </section>
</div>
</html>
PHP_EOL;


        $collection = $parser->parse(new ParseQuery('https://itc.ua/', $html, 2, false));
        self::assertEquals(2, $collection->count());

        $firstArticle = $collection->get(0);
        self::assertEquals(Article::class, get_class($firstArticle));
        /** @var Article $firstArticle */
        self::assertEquals(
            'EA пожизненно забанила игрока FIFA после его расистских оскорблений в адрес экс-футболиста Иана Райта',
            $firstArticle->getTitle()
        );
        self::assertEquals(
            'https://itc.ua/news/ea-pozhiznenno-zabanila-igroka-fifa-posle-ego-rasistskih-oskorblenij-v-adres-eks-futbolista-iana-rajta/',
            $firstArticle->getUrl()
        );
        self::assertEquals('2021-03-04T13:37:00+02:00', $firstArticle->getCreated()->format(DATE_ATOM));
        self::assertNull($firstArticle->getContent());

        $secondArticle = $collection->get(1);
        self::assertEquals(Article::class, get_class($secondArticle));
        /** @var Article $secondArticle */
        self::assertEquals(
            'Нова пошта і Postmen випустили робота-бібліотекаря «НП Лукаш», який допомагає підібрати книжку відвідувачам експозиції «Леся Українка: 150 імен»',
            $secondArticle->getTitle()
        );
        self::assertEquals(
            'https://itc.ua/news/nova-poshta-i-postmen-vipustili-robota-bibliotekarya-yakij-dopomaga%d1%94-pidibrati-knizhku-vidviduvacham-ekspoziczi%d1%97-lesya-ukra%d1%97nka-150-imen/',
            $secondArticle->getUrl()
        );
        self::assertEquals('2021-03-04T13:18:22+02:00', $secondArticle->getCreated()->format(DATE_ATOM));
        self::assertNull($secondArticle->getContent());

    }

}
