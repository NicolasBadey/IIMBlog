<?php
namespace App\Model\ETL;

class AbstractETLBuilder
{
    /**
     * @var ExtractInterface
     */
    protected $extract;

    /**
     * @var TransformInterface
     */
    protected $transform;

    /**
     * @var LoadInterface
     */
    protected $load;

    /**
     * @var ETL
     */
    protected $etl;


    /**
     * @return ETL
     */
    public function build(): ETL
    {
        if (null === $this->etl) {
            $this->etl = (ETL::create())
                ->setExtract($this->extract)
                ->setTransform($this->transform)
                ->setLoad($this->load);
        }

        return $this->etl;
    }
}
