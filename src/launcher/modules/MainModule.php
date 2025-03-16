<?php
namespace launcher\modules;

use std, gui, framework, launcher;


class MainModule extends AbstractModule
{
    function update($game,$path)
    {
        if ($this->accountName->text == null)
        {
            UXDialog::show('Please enter your Steam account login in the settings');
            return;
        }
        
        $this->playButton->enabled = false;
        $this->playButton->text = 'Updating';
        new Thread(function () use ($game,$path){
            $cli = new Process(['truckersmp-cli','-g',$path,'-n',$this->accountName->text,'-r',$this->backend->value,'update',$game.'mp'])->start();
            $cli->getInput()->eachLine(function ($l) use ($cli) {
                if (str::contains($l,'Cached credentials not found'))
                {
                    $cli->getOutput()->write(uiLaterAndWait(function (){return UXDialog::input('Enter your Steam password');})."\n");
                    $cli->getOutput()->flush();
                }
                
                if (str::contains($l,'progress:'))
                {
                    $progress = intval(str::sub($l,str::pos($l,'progress: ') + 10,str::lastPos($l,'(')));
                    uiLater(function () use ($progress) {
                        $this->progressBar->progress = $progress;
                        $this->progressLabel->text = 'Updating: '.$progress.'%';
                    });
                }
                else 
                    uiLater(function () use ($l) {$this->progressLabel->text = $l;});
                
                if (str::contains($l,'Invalid Password'))
                {
                    uiLater(function () use ($l){UXDialog::show('Invalid Password!','ERROR');});
                }   
                 
                uiLater(function () use ($l){app()->form('log')->textArea->text .= $l."\n";
                });
            });
            
            if ($cli->getExitValue() != 0 or str::contains(app()->form('log')->textArea->text,'FAILED'))
            {
                uiLater(function () use ($log) {
                    app()->showForm('log');
                    app()->form('log')->toast('Some kind of error occurred while the truckersmp-cli was running');
                    
                    $this->playButton->enabled = true;
                    $this->playButton->text = 'Play';
                });
                
                return;
            }
            
            uiLater(function () use ($game,$path){$this->runGame($game,$path);});
        })->start();
    }
    
    function runGame($game,$path)
    {
        $startArr = ['truckersmp-cli','-g',$path];
        $gameOptions = $game == 'ets' ? $this->etsConsole->text : $this->atsConsole->text;
        if ($gameOptions != null)
            $startArr = array_merge($startArr,[$gameOptions]);
        $startArr = array_merge($startArr,['start',$game.'mp']);
        
        $cli = new Process($startArr)->start();
        
        $this->toast('Have a nice trip!');
        $this->progressLabel->text = 'Game is starting';
        $this->progressBar->progress = -1;
        waitAsync('5s',function (){App::shutdown();});
    }
    
}