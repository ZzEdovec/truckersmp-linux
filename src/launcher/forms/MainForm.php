<?php
namespace launcher\forms;

use vdf\VDF;
use facade\Json;
use php\io\IOException;
use std, gui, framework, launcher;


class MainForm extends AbstractForm
{
    
    $page;
    
    function switchPage(UXButton $newPage)
    {
        if ($this->page == null) {
            $this->page['button'] = $this->main;
            $this->page['closeFunc'] = function ()
            {
                $this->newsContainer->hide();
                $this->progressBar->hide();
                $this->gameSelector->hide();
                $this->playButton->hide();
                $this->progressLabel->hide();
            };
        }
        
        $this->page['button']->textColor = '#e6e6e6';
        $newPage->textColor = 'White';
        $this->page['closeFunc']();
        
        switch ($newPage->id) {
            case ('main'):
                $this->newsContainer->show();
                $this->progressBar->show();
                $this->gameSelector->show();
                $this->playButton->show();
                $this->progressLabel->show();
                $this->tabLabel->text = 'News';
                $this->page['closeFunc'] = function ()
                {
                    $this->newsContainer->hide();
                    $this->progressBar->hide();
                    $this->progressLabel->hide();
                    $this->gameSelector->hide();
                    $this->playButton->hide();
                };
            break;
            /*case ('events'):
                $this->eventsContainer->show();
                $this->tabLabel->text = 'Events';
                $this->page['closeFunc'] = function (){$this->eventsContainer->hide();};
            break;*/
            case ('settings'):
                $this->settingsPanel->show();
                $this->tabLabel->text = 'Settings';
                $this->page['closeFunc'] = function (){$this->settingsPanel->hide();};
            break;
        }
        
        $this->page['button'] = $newPage;
    }
    
    /**
     * @event button7.action 
     */
    function doButton7Action(UXEvent $e = null)
    {    
        execute('xdg-open https://truckersmp.com/status');
    }

    /**
     * @event showing 
     */
    function doShowing(UXWindowEvent $e = null)
    {
        $newsVBOX = new UXVBox;
        $this->newsContainer->content = $newsVBOX;
        
        $communityMenu = new UXContextMenu;
        $forumItem = new UXMenuItem('Forum');
        $discordItem = new UXMenuItem('Discord');
        $patreonItem = new UXMenuItem('Patreon');
        $websiteItem = new UXMenuItem('Website');
        $forumItem->on('action',function (){execute('xdg-open https://forum.truckersmp.com');});
        $discordItem->on('action',function (){execute('xdg-open https://discord.com/invite/truckersmp');});
        $patreonItem->on('action',function (){execute('xdg-open https://www.patreon.com/truckersmp_official');});
        $websiteItem->on('action',function (){execute('xdg-open https://truckersmp.com');});
        $communityMenu->items->addAll([$forumItem,$discordItem,$patreonItem,$websiteItem]);
        $communityMenu->width = $this->communityButton->width;
        $this->communityButton->data('menu',$communityMenu);
        
        $socialMenu = new UXContextMenu;
        $instagramItem = new UXMenuItem('Instagram');
        $youtubeItem = new UXMenuItem('YouTube');
        $facebookItem = new UXMenuItem('Facebook');
        $twitterItem = new UXMenuItem('Twitter');
        $twitchItem = new UXMenuItem('Twitch');
        $githubItem = new UXMenuItem('GitHub');
        $instagramItem->on('action',function (){execute('xdg-open https://www.instagram.com/truckersmp.official');});
        $youtubeItem->on('action',function (){execute('xdg-open https://www.youtube.com/TruckersMPOfficial');});
        $facebookItem->on('action',function (){execute('xdg-open https://www.facebook.com/truckersmpofficial');});
        $twitterItem->on('action',function (){execute('xdg-open https://x.com/truckersmp');});
        $twitchItem->on('action',function (){execute('xdg-open https://www.twitch.tv/TruckersMP_Official');});
        $githubItem->on('action',function (){execute('xdg-open https://github.com/ZzEdovec/truckersmp-linux');});
        $socialMenu->items->addAll([$instagramItem,$youtubeItem,$facebookItem,$twitterItem,$twitchItem,$githubItem]);
        $socialMenu->width = $this->socialButton->width;
        $this->socialButton->data('menu',$socialMenu);
        
        $contactMenu = new UXContextMenu;
        $supportItem = new UXMenuItem('Support');
        $feedbackItem = new UXMenuItem('Feedback');
        $supportItem->on('action',function (){execute('xdg-open https://truckersmp.com/support');});
        $feedbackItem->on('action',function (){execute('xdg-open https://github.com/ZzEdovec/truckersmp-linux/issues');});
        $contactMenu->items->addAll([$supportItem,$feedbackItem]);
        $contactMenu->width = $this->contactButton->width;
        $this->contactButton->data('menu',$contactMenu);
        
        $this->gameSelector->data('graphic-ets',$this->gameSelector->graphic);
        $this->gameSelector->data('graphic-ats',new UXImageView(new UXImage('res://.data/img/amtruck.png')));
        
        
        try {
            $news = Json::decode(fs::get('https://api.truckersmp.com/v2/news'));
            if ($news['error'] == true)
                throw new IOException;
            foreach ($news['response']['news'] as $n) {
                if ($n['front_page'] == false)
                    continue;
                
                $newsItem = $this->instance('prototypes.news');
                
                $shadow = new UXDropShadowEffect;
                $shadowAlt = new UXDropShadowEffect;
                $shadow->color = $shadowAlt->color = 'Black';
                $shadow->spread = $shadowAlt->spread = 0.55;
                
                $newsItem->children[1]->image = UXImage::ofUrl($n['header_image_url']);
                $newsItem->children[3]->text = $n['title'];
                $newsItem->children[5]->text = $n['content_summary'];
                $newsItem->children[3]->effects->add($shadow);
                $newsItem->children[5]->effects->add($shadowAlt);
                $newsItem->children[7]->classesString = 'button main-button';
                $newsItem->children[7]->cursor = 'HAND';
                $newsItem->children[7]->on('action',function () use ($n) {
                    execute('xdg-open https://truckersmp.com/blog/'.$n['id']);
                })
                
                $this->newsContainer->content->add($newsItem);
            }
        } catch (IOException $ex) {
            UXDialog::show('Failed to fetch news, check network connection','ERROR');
        }
        
        $this->etsPath->text = $this->appModule()->ini->get('etsPath','Game directories');
        $this->etsConsole->text = $this->appModule()->ini->get('etsParams','Game params');
        $this->atsPath->text = $this->appModule()->ini->get('atsPath','Game directories');
        $this->atsConsole->text = $this->appModule()->ini->get('atsParams','Game params');
        $this->accountName->text = $this->appModule()->ini->get('accountLogin','Steam');
        $this->backend->value = $this->appModule()->ini->get('renderBackend','Game params') ?? 'DirectX11 (DXVK)';
        $this->gamemode->selected = $this->appModule()->ini->get('gamemodeUse','System');
        $this->libraryfoldersSave->selected = $this->appModule()->ini->get('libraryFoldersSave','Steam');
    }

    /**
     * @event communityButton.click 
     */
    function doCommunityButtonClick(UXMouseEvent $e = null)
    {    
        $e->sender->data('menu')->showByNode($e->sender,0,$e->sender->height);
    }

    /**
     * @event socialButton.click 
     */
    function doSocialButtonClick(UXMouseEvent $e = null)
    {    
        $e->sender->data('menu')->showByNode($e->sender,0,$e->sender->height);
    }

    /**
     * @event contactButton.click 
     */
    function doContactButtonClick(UXMouseEvent $e = null)
    {    
        $e->sender->data('menu')->showByNode($e->sender,0,$e->sender->height);
    }

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

    /**
     * @event events.action 
     */
    function doEventsAction(UXEvent $e = null)
    {    
        #$this->switchPage($this->events);
        execute('xdg-open https://truckersmp.com/events');
    }

    /**
     * @event main.action 
     */
    function doMainAction(UXEvent $e = null)
    {    
        $this->switchPage($this->main);
    }

    /**
     * @event settings.action 
     */
    function doSettingsAction(UXEvent $e = null)
    {    
        $this->switchPage($this->settings);
    }

    /**
     * @event etsPath.click 
     */
    function doEtsPathClick($e = null)
    {    
        $fc = new UXDirectoryChooser;
        $dir = $fc->showDialog($this);
        
        if ($dir == null)
            return;
        if (fs::isDir($dir.'/bin') == false) {
            $this->toast('Specify the root folder of the game!');
            return;
        }
        if (fs::isDir($dir.'/bin/win_x64') == false) {
            UXDialog::showAndWait('Windows version of the game is not detected!','ERROR');
            app()->showForm('switchHelp');
            return;
        }
        
        if ($e instanceof UXMouseEvent)
        {
            $this->appModule()->ini->set('etsPath',$dir,'Game directories');
            $e->sender->text = $dir;
        }
        else 
            return $dir;
    }

    /**
     * @event atsPath.click 
     */
    function doAtsPathClick(UXMouseEvent $e = null)
    {    
        $dir = $this->doEtsPathClick();
        if ($dir == null)
            return;
        
        $this->appModule()->ini->set('atsPath',$dir,'Game directories');
        $this->atsPath->text = $dir;
    }

    /**
     * @event etsConsole.keyUp 
     */
    function doEtsConsoleKeyUp(UXKeyEvent $e = null)
    {    
        $this->appModule()->ini->set('etsParams',$e->sender->text,'Game params');
    }

    /**
     * @event atsConsole.keyUp 
     */
    function doAtsConsoleKeyUp(UXKeyEvent $e = null)
    {    
        $this->appModule()->ini->set('atsParams',$e->sender->text,'Game params');
    }

    /**
     * @event accountName.keyUp 
     */
    function doAccountNameKeyUp(UXKeyEvent $e = null)
    {    
        $this->appModule()->ini->set('accountLogin',$e->sender->text,'Steam');
    }

    /**
     * @event backend.action 
     */
    function doBackendAction(UXEvent $e = null)
    {    
        $this->appModule()->ini->set('renderBackend',$e->sender->value,'Game params');
        
        if ($e->sender->value != 'DirectX11 (DXVK)')
            UXDialog::show('Using something other than DXVK can significantly reduce performance!','WARNING');
    }

    /**
     * @event playButton.click 
     */
    function doPlayButtonClick(UXMouseEvent $e = null)
    {
        if ($e->button == 'SECONDARY')
        {
            $needUpdate = true;
            $this->toast('Right mouse button is pressed, forcing update');
        }
        
        if (fs::isFile('/usr/bin/truckersmp-cli') == false)
        {
            UXDialog::show('Please install truckersmp-cli system-wide! (/usr/bin/truckersmp-cli)','ERROR');
            execute('xdg-open https://github.com/truckersmp-cli/truckersmp-cli/#install');
            return;
        }
        
        if ($this->gameSelector->text == 'Euro Truck Simulator 2')
        {
            $g = 'ets2';
            $path = $this->etsPath->text;
            $appID = 227300;
        }
        else 
        {
            $g = 'ats';
            $path = $this->atsPath->text;
            $appID = 270880;
        }
        
        if ($path == null)
        {
            UXDialog::show('Please specify the path to the game in the settings','ERROR');
            return;
        }
        
        if ($this->accountName->text == null)
        {
            UXDialog::show('Please enter your Steam account login in the settings','ERROR');
            return;
        }
        
        if (str::contains($path,'steamapps') == false)
            $this->toast('The game is not in the Steam Library directory! Updates will only be checked when the TruckersMP is updated');
        else 
        {
            $steamapps = str::sub($path,0,str::pos($path,'steamapps/') + 10);
            $buildID = VDF::fromFile($steamapps.'appmanifest_'.$appID.'.acf')['AppState']['buildid'];
            if ($this->appModule()->ini->get($g.'BuildID','Versions') != $buildID)
            {
                $this->appModule()->ini->set($g.'BuildID',$buildID,'Versions');
                $needUpdate = true;
            }
        }
        
        $tmpLast = Json::decode(fs::get('https://update.ets2mp.com/packages.json'))['CurrentVersion'];
        if ($this->appModule()->ini->get('tmp','Versions') != $tmpLast)
        {
            $this->appModule()->ini->set('tmp',$tmpLast,'Versions');
            $needUpdate = true;
        }
        
        if ($needUpdate)
            $this->update($g,$path);
        else 
            $this->runGame($g,$path);
    }

    /**
     * @event donateButton.action 
     */
    function doDonateButtonAction(UXEvent $e = null)
    {    
        execute('xdg-open https://www.donationalerts.com/r/queinu');
    }

    /**
     * @event link.action 
     */
    function doLinkAction(UXEvent $e = null)
    {    
        $this->toast("In the libraryfolders.vdf file, Steam stores the libraries you've added on other disks. When updating via SteamCMD they are often reset, this feature prevents that from happening.");
    }

    /**
     * @event libraryfoldersSave.click 
     */
    function doLibraryfoldersSaveClick(UXMouseEvent $e = null)
    {    
        $this->appModule()->ini->set('libraryFoldersSave',$e->sender->selected,'Steam');
    }

    /**
     * @event gamemode.click 
     */
    function doGamemodeClick(UXMouseEvent $e = null)
    {
        if (fs::isFile('/usr/bin/gamemoderun') == false)
        {
            UXDialog::show('Gamemode not installed!','ERROR');
            uiLater(function (){$this->gamemode->selected = false;});
            return;
        }
        
        $this->appModule()->ini->set('gamemodeUse',$e->sender->selected,'System');
    }


}
