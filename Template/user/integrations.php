<h3><i class="fa fa-google fa-fw"></i> <?= t('Google Account') ?></h3>

<div class="panel">
    <?= $this->form->hidden('google_show_avatar', array('google_show_avatar' => 0)) ?>
    <?= $this->form->checkbox('google_show_avatar', t('Show Google Avatar'), 1, isset($values['google_show_avatar']) && $values['google_show_avatar'] == 1) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</div>
