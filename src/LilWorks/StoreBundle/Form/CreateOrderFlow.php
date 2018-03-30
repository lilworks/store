<?php
namespace LilWorks\StoreBundle\Form;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class CreateOrderFlow extends FormFlow {

    protected function loadStepsConfig() {
        return array(
            array(
                'label' => 'customer',
                'form_type' => 'LilWorks\StoreBundle\Form\Flow\CustomerType',
            ),
            /*array(
                'label' => 'products',
                'form_type' => 'MyCompany\MyBundle\Form\CreateVehicleStep2Form',
                'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
                    return $estimatedCurrentStepNumber > 1 && !$flow->getFormData()->canHaveEngine();
                },
            ),*/
            array(
                'label' => 'confirmation',
            ),
        );
    }

}