<?php
// src/Acme/UserBundle/Entity/User.php

namespace DS\TxinbometroBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="txinbometro_usuario")
 * @ORM\Entity(repositoryClass="DS\TxinbometroBundle\Repository\UsuarioRepository")
 */
class Usuario extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="Vehiculo", inversedBy="usuario", cascade={"persist"})  
     * @ORM\JoinColumn(name="ultimo_vehiculo", referencedColumnName="id")
     * 
     * Ultimo vehiculo con el que trabaja el usuario
     */
    protected $vehiculo;
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set vehiculo
     *
     * @param DS\TxinbometroBundle\Entity\Vehiculo $vehiculo
     */
    public function setVehiculo(\DS\TxinbometroBundle\Entity\Vehiculo $vehiculo)
    {
        $this->vehiculo = $vehiculo;
    }

    /**
     * Get vehiculo
     *
     * @return DS\TxinbometroBundle\Entity\Vehiculo 
     */
    public function getVehiculo()
    {
        return $this->vehiculo;
    }
}