<?php

namespace quoma\modules\log;

use quoma\core\menu\Menu;
use quoma\core\module\QuomaModule;
use Yii;

class LogModule extends QuomaModule
{

    public $controllerNamespace = 'quoma\modules\log\controllers';

    public function init()
    {
        parent::init();

        $this->registerTranslations();
    }

    /**
     * @return Menu
     */
    public function getMenu(Menu $menu)
    {
        $_menu = (new Menu(Menu::MENU_TYPE_ITEM))->setLabel(Yii::t('app', 'Logs'))->setUrl(['/log'])->setVisible(Yii::$app->user->isSuperadmin);

        $menu->addItem($_menu, Menu::MENU_POSITION_FIRST);

        return $_menu;
    }

    public function getDependencies()
    {
        return [];
    }

}
