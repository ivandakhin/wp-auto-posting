<?php //require __DIR__ . '/partials/header.php'; ?>

<div class="wrap">
    <h2 class="ui header">
        wpAuto <span style="color: #f0506e">Posting</span>
        <div class="sub header">Розробка плагіна для WordPress із використанням OpenAI API.</div>
    </h2>
    <div id="ajax-data" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"></div>

    <div class="ui ordered three steps wp-auto-posting-steps">
        <div class="<?= ! $isApiKeyValid ? 'active' : 'completed'; ?> step wp-auto-posting-step-1">
            <div class="content">
                <div class="title">Setup</div>
                <div class="description">Enter API key and configure settings</div>
            </div>
        </div>
        <div class="<?= $isApiKeyValid ? 'active' : ''; ?> step wp-auto-posting-step-2">
            <div class="content">
                <div class="title">Generation</div>
                <div class="description">Generate content using OpenAI API</div>
            </div>
        </div>
        <div class="step wp-auto-posting-step-3">
            <div class="content">
                <div class="title">Publishing</div>
                <div class="description">Save draft or publish the post</div>
            </div>
        </div>
    </div>

    <div class="<?= ! $isApiKeyValid ? 'active' : 'completed'; ?> step wp-auto-posting-step wp-auto-posting-step-1-content ">
        <div class="ui form" id="wp-auto-posting-api-key-form">
            <div class="field">
                <label>API Key</label>
                <input id="wp-auto-posting-api-key-input" type="password" name="api-key" value="<?= $api_key; ?>"
                       placeholder="sk-...">
            </div>
            <button id="wp-auto-posting-api-key-save-button" class="ui button primary" type="button">Save</button>
            <button id="wp-auto-posting-step-1-next-button" class="ui button secondary" type="button" <?= ! $isApiKeyValid ? 'disabled' : ''; ?>>Next</button>
        </div>

        <div id="wp-auto-posting-api-key-status-success"
             class="ui green message transition" <?= ! $isApiKeyValid ? 'hidden' : ''; ?>>
            <div class="header">
                Your API Key is valid.
            </div>
            <p>You can now proceed to generate articles using OpenAI.</p>
        </div>

        <div id="wp-auto-posting-api-key-status-fail"
             class="ui orange message transition" <?= $isApiKeyValid ? 'hidden' : ''; ?>>
            <div class="header">
                Your API Key is not valid or empty.
            </div>
            <p>Error: Please try again and ensure the key is valid.</p>
        </div>
    </div>

    <div class="<?= $isApiKeyValid ? 'active' : ''; ?> step wp-auto-posting-step wp-auto-posting-step-2-content ">
        <div class="ui form" id="wp-auto-posting-api-key-form">
            <div class="field">
                <label>Prompt</label>
                <textarea id="wp-auto-posting-generate-post-textarea" type="password" name="prompt" placeholder="How to Successfully Complete Test Assignments for Job Interviews"></textarea>
            </div>
            <button id="wp-auto-posting-step-2-back-button" class="ui button secondary" type="button">Back</button>
            <button id="wp-auto-posting-generate-post-button" class="ui button primary" type="button">Generate Post</button>
        </div>

        <h2>Latest Prompts:</h2>
        <div class="ui relaxed divided list" id="wp-auto-posting-latest-prompts">
            <div class="item">
                <div class="content">
                    <a class="header">Semantic-Org/Semantic-UI</a>
                    <div class="description">Updated 10 mins ago</div>
                </div>
            </div>
            <div class="item">
                <div class="content">
                    <a class="header">Semantic-Org/Semantic-UI-Docs</a>
                    <div class="description">Updated 22 mins ago</div>
                </div>
            </div>
            <div class="item">
                <div class="content">
                    <a class="header">Semantic-Org/Semantic-UI-Meteor</a>
                    <div class="description">Updated 34 mins ago</div>
                </div>
            </div>
        </div>
    </div>

    <div class="step wp-auto-posting-step wp-auto-posting-step-3-content">
        <div class="ui padded segment" id="wp-auto-posting-generated-post-title"></div>
        <div class="ui padded segment" id="wp-auto-posting-generated-post-content"></div>

        <div class="ui form" id="wp-auto-posting-api-key-form">
            <button id="wp-auto-posting-step-3-back-button" class="ui button secondary" type="button">Change Prompt</button>
            <a id="wp-auto-posting-publishing-save-draft-post-button" class="ui button primary" type="button">Save Draft</a>
            <a id="wp-auto-posting-publishing-publish-post-button" class="ui button primary" type="button">Publish</a>
        </div>
    </div>

    <!--    <div class="uk-text-center wp-auto-posting-settings" uk-grid>-->
    <!--        <div class="uk-width-3-4 uk-padding uk-padding-remove-vertical">-->
    <!--            <input name="wp_openai_api_key" id="wp-auto-posting-settings-api-key-input" class="uk-input" type="password" value="-->
	<?php //= $api_key; ?><!--" placeholder="Enter your OpenAI API key">-->
    <!--        </div>-->
    <!--        <button class="uk-button uk-button-danger uk-width-1-4" id="wp-auto-posting-settings-api-key-save-button" type="button">Save</button>-->
    <!--    </div>-->

    <!--    <div class="uk-margin-remove-top">-->
    <!--        <div class="uk-margin-small-top" id="wp-auto-posting-settings-status">-->
    <!--            --><?php //if( ! $api_key ): ?>
    <!--                <span class="uk-label uk-label-danger">API Key is empty. Add your OpenAI API key 👆🏻</span>-->
    <!--            --><?php //endif; ?>
    <!--        </div>-->
    <!--    </div>-->

    <!--    <hr class="uk-margin-xsmall-top">-->

    <!--    <div class="uk-text-center" uk-grid>-->
    <!--        <div class="uk-width-3-4 uk-padding uk-padding-remove-vertical">-->
    <!--            <textarea id="wp-auto-posting-openai-prompt-area" class="uk-textarea uk-padding-small" rows="3" placeholder="Enter topic to generate post. (Промпт має лише визначати тему, про що писати)" aria-label="Textarea" </textarea>-->
    <!--        </div>-->
    <!--        <button id="wp-auto-posting-openai-send-prompt-button" class="uk-button uk-button-primary uk-width-1-4" type="button">Generate Post</button>-->
    <!--    </div>-->

    <!--    <hr>-->

    <div class="uk-card uk-card-default uk-card-body" id="wp-auto-posting-openai-content-wrapper" hidden>
        <h2 class="uk-article-title" id="wp-auto-posting-openai-title"></h2>
        <hr>
        <article id="wp-auto-posting-openai-content" class="uk-article uk-overflow-auto" uk-overflow-auto=""
                 style="min-height: 150px; max-height: 500px;"></article>
        <div class="uk-modal-footer uk-text-right uk-margin-medium-top">
            <button class="uk-button uk-button-default uk-modal-close" id="wp-auto-posting-openai-content-cancel"
                    type="button">Cancel
            </button>
            <button class="uk-button uk-button-secondary" type="button" id="wp-auto-posting-openai-content-save-draft">
                Save Draft
            </button>
            <button class="uk-button uk-button-primary" type="button" id="wp-auto-posting-openai-content-publish">
                Publish
            </button>
        </div>
    </div>

</div>
<?php //require __DIR__ . '/partials/footer.php'; ?>
