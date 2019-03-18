<?php
namespace App\Controller;

use App\Entity\Article;
use App\Model\ClientElasticSearch;
use App\Form\ArticleType;
use App\Model\ETL\LoadArticle;
use App\Model\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;


use App\Model\Newsletter;

class BlogController extends AbstractController
{
    /**
     * @var ClientElasticSearch
     */
    protected $client;

    /**
     * @var LoadArticle
     */
    protected $loadArticle;

    /**
     * BlogController constructor.
     * @param ClientElasticSearch $client
     * @param LoadArticle $loadArticle
     */
    public function __construct(ClientElasticSearch $client, LoadArticle $loadArticle)
    {
        $this->client = $client;
        $this->loadArticle = $loadArticle;
    }

    /**
     * @Route("/", name="home")
     */
    public function home(Request $request)
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/article/search", name="front_article_search")
     */
    public function article(Request $request)
    {

        //https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html

        $params = [
            'index' => $this->loadArticle->getAlias(),
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $request->get('search', ''),
                        'fields' => [
                            'title^3',
                            'content'
                        ],
                        'minimum_should_match' => '50%',
                        'type' => 'most_fields',
                        'fuzziness' => 'AUTO',
                        //'operator' => 'and', //look at cross_fields type before use operator "and"
                    ]
                ],
            ]
        ];

        /*
                $params = [
                    'index' => 'article',
                    'type' => 'doc',
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    'multi_match' => [
                                        'query' =>    $search,
                                        'fields' => [
                                            'title^3',
                                            'content'
                                        ],
                                        'minimum_should_match' => '50%',
                                        'type' => 'most_fields',
                                        'fuzziness' => 'AUTO',
                                        //'operator' => 'and', //look at cross_fields type before use operator "and"
                                    ]
                                ],

                                // complete geo-location distance here with :
                                // https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html#_lat_lon_as_properties_3
                            ]
                        ]
                    ]
                ];
        */

        $result = $this->client->search($params);

        return $this->render('blog/article.html.twig', [
            'articles' => $result['hits']['hits'],
        ]);
    }

    /**
     * @Route("/article/add", name="front_article_add")
     */
    public function articleCreate(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            //add a success FlashBag here

            return $this->redirectToRoute('article_list'); //create the action article_list
        }

        return $this->render('blog/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
