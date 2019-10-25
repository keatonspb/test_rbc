<?php


namespace app\commands;


use app\models\Article;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use yii\console\Controller;
use yii\console\ExitCode;


class GrabberController extends Controller
{
    const BASE_URL = "https://www.rbc.ru/";
    private static $base_headers = [
        'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0",
        "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
    ];
    private $Client;

    public function actionIndex()
    {
        $client = $this->getClient();
        $response = $client->get("/");
        $crawler = new Crawler($response->getBody()->getContents());
        $crawler->filterXPath("//div[contains(@class, 'js-news-feed-list')]/a[contains(@class, 'news-feed__item')]")
            ->each(function (Crawler $node, $i) {
                $link = $node->attr("href");

                if (!$link) {
                    return;
                }
                $titleNode = $node->filterXPath("//span[contains(@class, 'news-feed__item__title')]")->first();
                if (!$titleNode->count()) {
                    return;
                }
                $title = trim($titleNode->text());

                $id = str_replace("id_newsfeed_", "", $node->attr("id"));
                if ($id) {
                    if (!($Article = Article::find()->where(['rbc_hash' => $id])->one())) {
                        $Article = new Article();
                        $Article->rbc_hash = $id;
                    }
                } else {
                    $Article = new Article();
                }

                $Article->modified_at = date("Y-m-d H:i:s", $node->attr("data-modif") ?? time());
                echo $title . " " . $link . " ". $Article->modified_at . PHP_EOL;
                $Article->link = $link;
                $Article->title = $title;

                $this->getArticleContent($link, $Article);
                if($Article->content) {
                    $Article->save();
                }

            });
        return ExitCode::OK;

    }

    private function getClient(): Client
    {
        if (!$this->Client) {
            $this->Client = new Client([
                'base_uri' => self::BASE_URL,
                'headers' => self::$base_headers,
            ]);
        }
        return $this->Client;
    }

    private function getArticleContent(string $url, Article &$article)
    {
        $this->stdout("Gettiing ".$url.PHP_EOL);
        $client = $this->getClient();
        $response = $client->get($url);
        $crawler = new Crawler($response->getBody()->getContents());
        $articleBlock = $crawler->filterXPath("//div[@itemprop='articleBody']")->first();
        if ($articleBlock->count()) {
            $article->content = trim($articleBlock->html());
        }

        $imageBlock = $crawler->filterXPath("//mata[@name='twitter:image']")->first();
        if ($imageBlock->count()) {
            $article->image_link = trim($imageBlock->html());
        }


    }


}
