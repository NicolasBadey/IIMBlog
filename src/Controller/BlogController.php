<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Model\ElasticSearchClient;
use App\Model\ETL\Article\ArticleLoad;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @var ElasticSearchClient
     */
    protected $client;

    /**
     * BlogController constructor.
     */
    public function __construct(ElasticSearchClient $client)
    {
        $this->client = $client;
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
            'index' => ArticleLoad::getAlias(),
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $request->get('search', ''),
                        'fields' => [
                            'title^3',
                            'content',
                        ],
                        'minimum_should_match' => '50%',
                        'type' => 'most_fields',
                        'fuzziness' => 'AUTO',
                        //'operator' => 'and', //look at cross_fields type before use operator "and"
                    ],
                ],
            ],
        ];

        /*
         * if geo distance :
         *
         * https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html
         *
                $params = [
                    'index' => ArticleLoad::getAlias(),
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
                                'filter' : [
                                    'geo_distance' : [
                                        'distance' : '200km',
                                        'pin.location' : [
                                            'lat' : 40,
                                            'lon' : -70
                                        ]
                                    ]
                                ]
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

            $this->addFlash('succes', 'your message');

            return $this->redirectToRoute('article_list'); //create the action article_list
        }

        return $this->render('blog/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
