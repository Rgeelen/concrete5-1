<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$th = Loader::helper('text');
$ip = Loader::helper('validation/ip'); ?>
<style>
    td.hidden-actions {
        display: none;
    }
</style>
<div class="ccm-dashboard-content-full">

    <div data-search-element="wrapper">
        <form role="form" data-search-form="logs" action="<?=$controller->action('view')?>" class="form-inline ccm-search-fields">
            <div class="ccm-search-fields-row">
                <div class="form-group">
                    <?=$form->label('keywords', t('Search'))?>
                    <div class="ccm-search-field-content">
                        <div class="ccm-search-main-lookup-field">
                            <i class="fa fa-search"></i>
                            <?=$form->search('cmpMessageKeywords', array('placeholder' => t('Keywords')))?>
                            <button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ccm-search-fields-row">
                <div class="form-group">
                    <?=$form->label('cmpMessageFilter', t('Filter by Flag'))?>
                    <div class="ccm-search-field-content">
                        <?=$form->select('cmpMessageFilter', $cmpFilterTypes, $cmpMessageFilter) ?>
                    </div>
                </div>
            </div>
            <div class="ccm-search-fields-row">
                <div class="form-group form-group-full">
                    <?=$form->label('cmpMessageSort', t('Sort By'))?>
                    <div class="ccm-search-field-content">
                        <?=$form->select('cmpMessageSort', $cmpSortTypes)?>
                    </div>
                </div>
            </div>

            <div class="ccm-search-fields-submit">
            <button type="submit" class="btn btn-primary pull-right"><?=t('Search')?></button>
            </div>

        </form>

    </div>

    <div data-search-element="results">
        <div class="table-responsive">
            <table class="ccm-search-results-table">
                <thead>
                <tr>
                    <th class="<?=$list->getSearchResultsClass('cnvMessageDateCreated')?>"><a href="<?=$list->getSortByURL('cnvMessageDateCreated', 'desc')?>"><?=t('Posted')?></a></th>
                    <th><span><?=t('Author')?></span></th>
                    <th><span><?=t('Message')?></span></th>
                    <th style="text-align: center"><span><?=t('Status')?></span></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($messages) > 0) {
                    $dh = Core::make('date');
                    foreach($messages as $msg) {
                        $cnv = $msg->getConversationObject();
                        if(is_object($cnv)) {
                            $page = $cnv->getConversationPageObject();
                        }
                        $msgID = $msg->getConversationMessageID();
                        $cnvID = $cnv->getConversationID();
                        $author = $msg->getConversationMessageAuthorObject();
                        $formatter = $author->getFormatter();
                        ?>
                        <tr>
                            <!-- <td><?=$form->checkbox('cnvMessageID[]', $msg->getConversationMessageID())?></td> -->
                            <td>
                                <?=$dh->formatDateTime(strtotime($msg->getConversationMessageDateTime()))?>
                            </td>
                            <td>
                                <p><?
                                    echo tc(/*i18n: %s is the name of the author */ 'Authored', 'By %s', $formatter->getLinkedAdministrativeDisplayName());
                                    ?></p>
                                <?

                                if (is_object($page)) { ?>
                                    <div><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=$page->getCollectionPath()?></a></div>
                                <? } ?>
                            </td>
                            <td class="message-cell" style="width: 33%">
                                <div class="ccm-conversation-message-summary">
                                    <div class="message-output">
                                        <?=$msg->getConversationMessageBodyOutput(true)?>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center">
                                <?
                                if (!$msg->isConversationMessageApproved() && !$msg->isConversationMessageDeleted()) { ?>
                                    <i class="fa fa-warning text-warning launch-tooltip" title="<?php echo t('Message has not been approved.')?>"></i>
                                <? }

                                if ($msg->isConversationMessageDeleted()) { ?>
                                    <i class="fa fa-trash launch-tooltip" title="<?php echo t('Message is deleted.')?>"></i>
                                <? }

                                if($msg->isConversationMessageFlagged()) { ?>
                                    <i class="fa fa-flag text-danger launch-tooltip" title="<?php echo t('Message is flagged as spam.')?>"></i>
                                <? }

                                if ($msg->isConversationMessageApproved() && !$msg->isConversationMessageDeleted()) { ?>
                                    <i class="fa fa-thumbs-up launch-tooltip" title="<?php echo t('Message is approved.')?>"></i>
                                <? } ?>
                            </td>
                            <td>
                                <?php if (is_object($page)) { ?>
                                    <a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>#cnv<?php echo $cnvID ?>Message<?php echo $msgID ?>" class="icon-link"><i class="fa fa-share"></i></a>
                                <?php } ?>
                            </td>
                            <td class="hidden-actions">
                                <div class="message-actions message-actions<?php echo $msgID ?>" data-id="<?php echo $msgID ?>">
                                    <ul>
                                        <li>
                                            <?php if($msg->isConversationMessageApproved()) { ?>
                                                <a class = "unapprove-message" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Unapprove') ?></a>
                                            <?php } else {  ?>
                                                <a class = "approve-message" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Approve') ?></a>
                                            <?php } ?>
                                        </li>
                                        <li>
                                            <?php if($msg->isConversationMessageDeleted()){ ?>
                                                <a class = "restore-message" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Restore') ?></a>
                                            <?php } else { ?>
                                                <a class = "delete-message" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Delete') ?></a>
                                            <?php } ?>
                                        </li>
                                        <li><?php if($msg->isConversationMessageFlagged()) { ?>
                                                <a class = "unmark-spam" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Unmark as spam') ?></a>
                                            <?php } else { ?>
                                                <a class = "mark-spam" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Mark as spam') ?></a>
                                            <?php } ?>
                                        </li>
                                        <li>
                                            <a class = "mark-user" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Mark all user posts as spam') ?></a>
                                        </li>
                                        <? /*
                                        <li>
                                            <?php if(is_object($ui) && $ui->isActive()) { ?>
                                                <a class = "deactivate-user" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Deactivate User') ?></a>
                                            <?php } else { ?>
                                                <span class="inactive"><?php echo t('User deactivated'); ?></span>
                                            <?php }?>
                                        </li>
                                         */ ?>
                                        <li>
                                            <?php if(!$ip->isBanned($msg->getConversationMessageSubmitIP())) { ?>
                                                <a class = "block-ip" data-rel-message-id="<?php echo $msgID ?>" href="#"><?php echo t('Block user IP Address') ?></a>
                                            <?php } else { ?>
                                                <span class="inactive"><?php echo t('IP Banned') ?></span>
                                            <?php } ?>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <? }
                }?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- END Body Pane -->
    <?=$list->displayPagingV2()?>

</div>
