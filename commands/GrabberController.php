<?php


namespace app\commands;


use app\models\Article;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Контроллер парсинга
 * Class GrabberController
 * @package app\commands
 */
class GrabberController extends Controller
{
    const BASE_URL = "https://www.rbc.ru/";
    const BASE_HEADERS = [ //Подменем заголовки
        'Origin' => "https://www.rbc.ru/",
        'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0",
        "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
    ];
    private $Client;

    /**
     * Парсит новости c главной
     * @return int
     */
    public function actionIndex()
    {
        $client = $this->getClient();
        $response = $client->get("/"); //Получаем исходный код главной страницы
        $crawler = new Crawler($response->getBody()->getContents());
        $crawler->filterXPath("//div[contains(@class, 'js-news-feed-list')]/a[contains(@class, 'news-feed__item')]") //Ищем новости через xpath
            ->each(function (Crawler $node) {
                $link = $node->attr("href");
                if (!$link) { //Нам не нада новость со ссылкой
                    return;
                }
                $titleNode = $node->filterXPath("//span[contains(@class, 'news-feed__item__title')]")->first();
                if (!$titleNode->count()) { //Если нод нет
                    return;
                }
                $title = trim($titleNode->text());

                $id = str_replace("id_newsfeed_", "", $node->attr("id")); //Получаем внешний id
                if ($id) {
                    if (!($Article = Article::find()->where(['rbc_hash' => $id])->one())) { // Проверяем, есть ли такая статья
                        $Article = new Article();
                        $Article->rbc_hash = $id;
                    }
                } else { //Статья без id нам тоже не нужна
                    return;
                }

                $Article->modified_at = date("Y-m-d H:i:s", $node->attr("data-modif") ?? time()); //Получаем дату статьи
                $this->stdout($title . " " . $link . " ". $Article->modified_at . PHP_EOL);
                $Article->link = $link;
                $Article->title = $title;

                $this->getArticleContent($link, $Article);
                if($Article->content) { //Нам нужна статья с контентом
                    $Article->save();
                }

            });
        return ExitCode::OK;

    }

    /**
     * HTTP клиент
     * @return Client
     */
    private function getClient(): Client
    {
        if (!$this->Client) {
            $this->Client = new Client([
                'base_uri' => self::BASE_URL,
                'headers' => self::BASE_HEADERS,
            ]);
        }
        return $this->Client;
    }

    private function getArticleContent(string $url, Article &$article)
    {
        $this->stdout("Gettiing ".$url.PHP_EOL);
        $client = $this->getClient();
        $response = $client->get($url); //Получаем исходный код статьи
        $crawler = new Crawler($response->getBody()->getContents());
        $articleBlock = $crawler->filterXPath("//div[@itemprop='articleBody']")->first(); //Ищем блок с контентом. Материал без метаданных является партнерским
        if ($articleBlock->count()) {
            $article->content = trim($articleBlock->html());
        }

        $imageBlock = $crawler->filterXPath("//meta[@property='og:image']")->first(); //Берем картинку из метатэга opengraph
        if ($imageBlock->count()) {
            $this->stdout("Image ".trim($imageBlock->attr("content")).PHP_EOL);
            $article->image_link = trim($imageBlock->attr("content"));
        }
    }

}
