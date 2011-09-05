<?php

namespace DS\TxinbometroBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * GasolinaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GasolinaRepository extends EntityRepository
{
    public function getAllFrom($vehiculo_id) {
        return $this->_em->createQuery('SELECT g FROM TxinbometroBundle:Gasolina g WHERE g.vehiculo = '.$vehiculo_id.' ORDER BY g.km')->getResult();
    }        
    
    /* Devuelve una tabla con la siguiente estructura
     * $tabla
     */
    public function getConsumos($vehiculo){
        //$dato_list=array();
        

        $dato_list=$this->_em->createQuery('SELECT g FROM TxinbometroBundle:Gasolina g WHERE g.vehiculo = '.$vehiculo->getId().' ORDER BY g.km')->getResult();//getArrayResult();  
        
        

        //Si hay datos, comienzo el estudio
        if($dato_list!=NULL) {
            // Inicializo las variables
            $km=array();
            $nDatos=array();
            $consumo=array(array());
            $frecuencia=array(array());
            $litros=array();
            $costeLitros=array();
            $kmDia=array();

            $lista = array();

            foreach (array('total', 'carretera', 'mixto', 'urbano') as $tipo) {
                $km[$tipo]=0;
                $nDatos[$tipo]=0;
                $dias[$tipo]=0;
                $litros[$tipo]=0;
                $costeLitros[$tipo]=0;
                $kmDia[$tipo]=0;
                foreach (array('maximo', 'medio') as $tipo2) {
                    $consumo[$tipo][$tipo2]=0;
                    $costeKm[$tipo][$tipo2]=0;
                    $frecuencia[$tipo][$tipo2]=0;
                }
                $consumo[$tipo]['minimo']=999;
                $costeKm[$tipo]['minimo']=999;
                $frecuencia[$tipo]['minimo']=999;
            }


            //Preparao el dato inicial y comienza el bucle
            $dato1=$dato_list[0];
            $dato2=$dato_list[0];

            for ($i=1;$i<count($dato_list);$i++) {
                // Obtengo los intervalos entre datos
                $dato2=$dato_list[$i];

                $lista[$i-1]['km_recorridos']=$dato2->getKm()-$dato1->getKm();
                $lista[$i-1]['dias']=($dato2->getFecha()->getTimestamp()-$dato1->getFecha()->getTimestamp())/86400;
                $lista[$i-1]['fecha']=$dato2->getFecha();
                $lista[$i-1]['comentario']=$dato2->getComentario();
                $lista[$i-1]['coste']=$dato2->getCoste();
                $lista[$i-1]['litros']=$dato2->getLitros();
                $lista[$i-1]['consumo']=100*($dato2->getLitros()/$lista[$i-1]['km_recorridos']);
                $lista[$i-1]['coste_km']=$dato2->getCoste()/$lista[$i-1]['km_recorridos'];
                $lista[$i-1]['tipo']=$dato2->getTipo();
                $lista[$i-1]['km']=$dato2->getKm();
                $lista[$i-1]['autonomia']=($vehiculo->getCapacidad()*100)/$lista[$i-1]['consumo'];
                $lista[$i-1]['km_restantes']=$lista[$i-1]['autonomia']-$lista[$i-1]['km_recorridos'];
                $lista[$i-1]['desposito_restante']=($lista[$i-1]['km_restantes']*100)/$lista[$i-1]['autonomia'];
                $lista[$i-1]['km_dia']=$lista[$i-1]['km_recorridos']/$lista[$i-1]['dias'];

                // Calculo otros valores derivados
                $km['total']+=$lista[$i-1]['km_recorridos'];
                $dias['total']+=$lista[$i-1]['dias'];
                $litros['total']+=$lista[$i-1]['litros'];
                $costeLitros['total']+=$lista[$i-1]['coste'];
                $consumo['total']['medio']+=$lista[$i-1]['consumo'];
                $costeKm['total']['medio']+=$lista[$i-1]['coste_km'];
                $frecuencia['total']['medio']+=$lista[$i-1]['dias'];
                if($consumo['total']['minimo']>$lista[$i-1]['consumo']) $consumo['total']['minimo']=$lista[$i-1]['consumo'];
                if($consumo['total']['maximo']<$lista[$i-1]['consumo']) $consumo['total']['maximo']=$lista[$i-1]['consumo'];
                if($costeKm['total']['minimo']>$lista[$i-1]['coste_km']) $costeKm['total']['minimo']=$lista[$i-1]['coste_km'];
                if($costeKm['total']['maximo']<$lista[$i-1]['coste_km']) $costeKm['total']['maximo']=$lista[$i-1]['coste_km'];
                if($frecuencia['total']['minimo']>$lista[$i-1]['dias']) $frecuencia['total']['minimo']=$lista[$i-1]['dias'];
                if($frecuencia['total']['maximo']<$lista[$i-1]['dias']) $frecuencia['total']['maximo']=$lista[$i-1]['dias'];

                $nDatos['total']++;

                //En funcion del tipo de consumo
                $km[$dato2->getTipo()]+=$lista[$i-1]['km_recorridos'];
                $dias[$dato2->getTipo()]+=$lista[$i-1]['dias'];
                $litros[$dato2->getTipo()]+=$lista[$i-1]['litros'];
                $costeLitros[$dato2->getTipo()]+=$lista[$i-1]['coste'];
                $consumo[$dato2->getTipo()]['medio']+=$lista[$i-1]['consumo'];
                $costeKm[$dato2->getTipo()]['medio']+=$lista[$i-1]['coste_km'];
                $frecuencia[$dato2->getTipo()]['medio']+=$lista[$i-1]['dias'];
                if($consumo[$dato2->getTipo()]['minimo']>$lista[$i-1]['consumo']) $consumo[$dato2->getTipo()]['minimo']=$lista[$i-1]['consumo'];
                if($consumo[$dato2->getTipo()]['maximo']<$lista[$i-1]['consumo']) $consumo[$dato2->getTipo()]['maximo']=$lista[$i-1]['consumo'];
                if($costeKm[$dato2->getTipo()]['minimo']>$lista[$i-1]['coste_km']) $costeKm[$dato2->getTipo()]['minimo']=$lista[$i-1]['coste_km'];
                if($costeKm[$dato2->getTipo()]['maximo']<$lista[$i-1]['coste_km']) $costeKm[$dato2->getTipo()]['maximo']=$lista[$i-1]['coste_km'];
                if($frecuencia[$dato2->getTipo()]['minimo']>$lista[$i-1]['dias']) $frecuencia[$dato2->getTipo()]['minimo']=$lista[$i-1]['dias'];
                if($frecuencia[$dato2->getTipo()]['maximo']<$lista[$i-1]['dias']) $frecuencia[$dato2->getTipo()]['maximo']=$lista[$i-1]['dias'];


                $nDatos[$dato2->getTipo()]++;

                //Preparo la siguiente iteracion
                $dato1=$dato2;
            }
            // Derivo algunos valores mas para los que no es necesario hacerlos sobre la iteracion anterior

            foreach (array('total', 'carretera', 'mixto', 'urbano') as $tipo) {
                $kmDia[$tipo]=$km[$tipo]/$dias[$tipo];
            }


            $listado=$lista;

            foreach (array('total', 'carretera', 'mixto', 'urbano') as $tipo) {
                $consumo[$tipo]['medio']=$consumo[$tipo]['medio']/$nDatos[$tipo];
                $costeKm[$tipo]['medio']=$costeKm[$tipo]['medio']/$nDatos[$tipo];
                $frecuencia[$tipo]['medio']=$frecuencia[$tipo]['medio']/$nDatos[$tipo];
            }

        }
        else {
            $listado=null;
        }
        
        return $listado;
        
    }    
}