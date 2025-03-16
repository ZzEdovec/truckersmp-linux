<?php
namespace launcher\forms;

use std, gui, framework, launcher;


class playForm extends AbstractForm
{

    /**
     * @event gameSelector.action 
     */
    function doGameSelectorAction(UXEvent $e = null)
    {
        if ($this->gameSelector->text == 'Euro Truck Simulator 2') {
            $this->gameSelector->graphic = $this->gameSelector->data('graphic-ats');
            $this->gameSelector->text = 'American Truck Simulator';
        }
        else {
            $this->gameSelector->graphic = $this->gameSelector->data('graphic-ets');
            $this->gameSelector->text = 'Euro Truck Simulator 2';
        }
    }

}
