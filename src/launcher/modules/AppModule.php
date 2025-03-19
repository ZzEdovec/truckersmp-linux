<?php
namespace launcher\modules;

use std, gui, framework, launcher;


class AppModule extends AbstractModule
{

    /**
     * @event action 
     */
    function doAction(ScriptEvent $e = null)
    {    
        if (fs::isFile($this->ini->path) == false)
        {
            $this->ini->set('libraryFoldersSave',true,'Steam');
            if (fs::isFile('/usr/bin/gamemoderun'))
                $this->appModule()->ini->set('gamemodeUse',true,'System');
        }
    }

}
