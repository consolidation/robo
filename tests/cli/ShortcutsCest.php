<?php

class ShortcutsCest
{
    public function _before(CliGuy $I)
    {
        $I->amInPath(codecept_data_dir('sandbox'));
    }

    public function useTheCopyDirShortcut(CliGuy $I)
    {
        $I->wantTo('copy dir with _copyDir shortcut');
        $I->shortcutCopyDir('box', 'bin');
        $I->seeDirFound('bin');
        $I->seeFileFound('robo.txt', 'bin');
    }

    public function useTheMirrorDirShortcut(CliGuy $I)
    {
        $I->wantTo('mirror dir with _mirrorDir shortcut');
        $I->shortcutMirrorDir('box', 'bin');
        $I->seeDirFound('bin');
        $I->seeFileFound('robo.txt', 'bin');
    }
}
