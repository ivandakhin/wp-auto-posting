(function ($) {
    'use strict';

    $(document).ready(function () {
        let ajaxUrl = $("#ajax-data").data("ajax-url");

        let settingsStepApiKeyInput = $('#wp-auto-posting-api-key-input');
        let settingsStepApiKeySaveButton = $('#wp-auto-posting-api-key-save-button');
        let settingsStepApiKeyStatusSuccess = $('#wp-auto-posting-api-key-status-success');
        let settingsStepApiKeyStatusFail = $('#wp-auto-posting-api-key-status-fail');
        let settingsStepApiKeyNextButton = $('#wp-auto-posting-step-1-next-button');
        let settingsStepApiKeyCard = $('.wp-auto-posting-step-1');
        let settingsStepApiKeyContent = $('.wp-auto-posting-step-1-content');
        let settingsStepApiKeyBackButton = $('#wp-auto-posting-step-2-back-button');

        let generateStepContent = $('.wp-auto-posting-step-2-content');
        let generateStepCard = $('.wp-auto-posting-step-2');
        let generateStepPostTextarea = $('#wp-auto-posting-generate-post-textarea');
        let generateStepPostStartButton = $('#wp-auto-posting-generate-post-button');
        let generateStepPostBackButton = $('#wp-auto-posting-step-3-back-button');

        let publishStepContent = $('.wp-auto-posting-step-3-content');
        let publishStepCard = $('.wp-auto-posting-step-3');
        let publishStepPostTitle = $('#wp-auto-posting-generated-post-title');
        let publishStepPostContent = $('#wp-auto-posting-generated-post-content');

        let publishStepSaveDraftPostButton = $('#wp-auto-posting-publishing-save-draft-post-button');
        let publishStepPublishPostButton = $('#wp-auto-posting-publishing-publish-post-button');


        function toggleSaveButton() {
            const isEmpty = !settingsStepApiKeyInput.val().trim();
            settingsStepApiKeySaveButton.prop('disabled', isEmpty);
        }

        function toogleGenerateButton() {
            const isEmpty = !generateStepPostTextarea.val().trim();
            generateStepPostStartButton.prop('disabled', isEmpty);
        }

        function timeSince(date) {
            let seconds = Math.floor((new Date() - date) / 1000);
            let intervals = {
                year: 31536000,
                month: 2592000,
                day: 86400,
                hour: 3600,
                minute: 60
            };

            for (let unit in intervals) {
                let value = Math.floor(seconds / intervals[unit]);
                if (value > 1) return `${value} ${unit}s ago`;
                if (value === 1) return `${value} ${unit} ago`;
            }

            return "just now";
        }

        function displayPromptHistory() {
            let historyContainer = $("#wp-auto-posting-latest-prompts");

            if (historyContainer.length === 0) return;

            historyContainer.empty();
            let history = JSON.parse(sessionStorage.getItem("user_prompt_history")) || [];

            if (history.length === 0) {
                historyContainer.html("<p>No prompts yet.</p>");
                return;
            }

            $.each(history.reverse(), function(index, entry) {
                let timeAgo = timeSince(new Date(entry.timestamp));

                let promptItem = $(`
                    <div class="item">
                        <div class="content">
                            <a class="header">${entry.prompt}</a>
                            <div class="description">Updated ${timeAgo}</div>
                        </div>
                    </div>
                `);

                historyContainer.append(promptItem);
            });

            historyContainer.find('.item').on('click', function () {
                generateStepPostTextarea.val( $(this).find('.header').text() );
                toogleGenerateButton();
            });
        }

        displayPromptHistory();

        settingsStepApiKeyInput.on('input', toggleSaveButton);
        toggleSaveButton();

        generateStepPostTextarea.on('input', toogleGenerateButton);
        toogleGenerateButton();

        settingsStepApiKeySaveButton.on('click', function () {
            let apiKey = settingsStepApiKeyInput.val().trim();
            let button = $(this);

            button.addClass('loading');

            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "save_open_ai_api_key",
                    api_key: apiKey
                },
                success: function (response) {
                    if (response.data.success) {
                        settingsStepApiKeyStatusFail.attr('hidden', true);
                        settingsStepApiKeyStatusSuccess.removeAttr('hidden');
                        settingsStepApiKeyNextButton.removeAttr('disabled');
                    } else {
                        settingsStepApiKeyStatusSuccess.attr('hidden', true);
                        settingsStepApiKeyStatusFail.removeAttr('hidden');
                        settingsStepApiKeyNextButton.attr('disabled', true);
                    }

                    button.removeClass('loading');
                },
                error: function (e) {
                    settingsStepApiKeyStatusSuccess.attr('hidden', true);
                    settingsStepApiKeyStatusFail.removeAttr('hidden');
                    button.removeClass('loading');
                }
            });
        });

        generateStepPostStartButton.on('click', function () {
            let prompt = generateStepPostTextarea.val().trim();
            let button = $(this);

            let timestamp = new Date().toISOString();
            let history = JSON.parse(sessionStorage.getItem("user_prompt_history")) || [];
            history.push({ prompt: prompt, timestamp: timestamp });

            sessionStorage.setItem("user_prompt_history", JSON.stringify(history));

            button.addClass('loading');

            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "get_open_ai_generated_post",
                    prompt: prompt
                },
                success: function (response) {
                    button.removeClass('loading');

                    publishStepPostTitle.html(`<h1 class="ui header">${response.data.title}</h1>`)
                    publishStepPostContent.html(response.data.content)

                    generateStepContent.addClass('completed').removeClass('active');
                    generateStepCard.addClass('completed').removeClass('active');

                    publishStepContent.addClass('active');
                    publishStepCard.addClass('active');
                },
                error: function (e) {
                    button.removeClass('loading');
                }
            });
        });

        settingsStepApiKeyNextButton.on('click', function () {
            settingsStepApiKeyCard.addClass('completed').removeClass('active');
            settingsStepApiKeyContent.addClass('completed').removeClass('active');

            generateStepContent.addClass('active');
            generateStepCard.addClass('active');

            displayPromptHistory();
        });

        settingsStepApiKeyBackButton.on('click', function () {
            settingsStepApiKeyCard.removeClass('completed').addClass('active');
            settingsStepApiKeyContent.removeClass('completed').addClass('active');

            generateStepContent.removeClass('active');
            generateStepCard.removeClass('active');
        });

        generateStepPostBackButton.on('click', function () {
            generateStepPostTextarea.val('');
            displayPromptHistory();

            generateStepContent.removeClass('completed').addClass('active');
            generateStepCard.removeClass('completed').addClass('active');

            publishStepPostTitle.html('');
            publishStepPostContent.html('');

            publishStepContent.removeClass('active');
            publishStepCard.removeClass('active');
        });

        publishStepSaveDraftPostButton.on('click', function () {
            let title = publishStepPostTitle.find('h1').html();
            let content = publishStepPostContent.html();
            let button = $(this);

            button.addClass('loading');

            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "save_draft_post",
                    title: title,
                    content: content,
                },
                success: function (response) {
                    button.removeClass('loading');
                    button.html('Show Draft');
                    button.attr('href', response.data.link).attr('target', '_blank');
                },
                error: function (e) {
                    button.removeClass('loading');
                }
            });
        });

        publishStepPublishPostButton.on('click', function () {
            let title = publishStepPostTitle.find('h1').html();
            let content = publishStepPostContent.html();
            let button = $(this);

            button.addClass('loading');

            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "publish_post",
                    title: title,
                    content: content,
                },
                success: function (response) {
                    button.removeClass('loading');
                    button.html('Show Post');
                    button.attr('href', response.data.link).attr('target', '_blank');
                },
                error: function (e) {
                    button.removeClass('loading');
                }
            });
        });
    });


    $(document).ready(function () {
        let ajaxUrl = $("#ajax-data").data("ajax-url");

        let settingsApiKeySaveButton = $('#wp-auto-posting-settings-api-key-save-button');
        let settingsApiKeyInput = $('#wp-auto-posting-settings-api-key-input');
        let settingsApiKeyStatus = $('#wp-auto-posting-settings-status');

        settingsApiKeySaveButton.on('click', function () {
            let apiKey = settingsApiKeyInput.val().trim();
            let button = $(this);

            button.html('<div uk-spinner="ratio: 0.7"></div>');

            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "save_open_ai_api_key",
                    api_key: apiKey
                },
                success: function (response) {
                    button.html('Save');
                    settingsApiKeyStatus.html(`<span class="uk-label uk-label-${response.data.status}">${response.data.message}</span>`)
                },
                error: function (e) {
                    button.html('Save');
                    console.log(e)
                }
            });
        });
    });

    $(document).ready(function () {
        let ajaxUrl = $("#ajax-data").data("ajax-url");

        let openAiPromptArea = $('#wp-auto-posting-openai-prompt-area');
        let openAiSendPromptButton = $('#wp-auto-posting-openai-send-prompt-button');
        let openAiContent = $('#wp-auto-posting-openai-content');
        let openAiTitle = $('#wp-auto-posting-openai-title');
        let openAiContentWrapper = $('#wp-auto-posting-openai-content-wrapper');

        let openAiContentCancel = $('#wp-auto-posting-openai-content-cancel');
        let openAiContentSaveDraft = $('#wp-auto-posting-openai-content-save-draft');
        let openAiContentPublish = $('#wp-auto-posting-openai-content-publish');


        openAiSendPromptButton.on('click', function () {
            let prompt = openAiPromptArea.val().trim();
            let button = $(this);

            button.html('<div uk-spinner="ratio: 0.7"></div>');

            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "get_open_ai_generated_post",
                    prompt: prompt
                },
                success: function (response) {
                    button.html('Generate Post');
                    openAiTitle.html(response.data.title)
                    openAiContent.html(response.data.content)
                    openAiContentWrapper.removeAttr("hidden").addClass("uk-animation-fade");
                },
                error: function (e) {
                    button.html('Generate Post');
                    console.log(e)
                }
            });
        });

        openAiContentCancel.on('click', function () {
            openAiContentWrapper.attr("hidden", true);
            openAiTitle.html('');
            openAiContent.html('');
            openAiPromptArea.val('');
        });

        openAiContentSaveDraft.on('click', function () {
            let title = openAiTitle.html();
            let content = openAiContent.html();

            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "save_draft_post",
                    title: title,
                    content: content,
                },
                success: function (response) {
                    console.log(response);
                    openAiContentWrapper.attr("hidden", true);
                    openAiTitle.html('');
                    openAiContent.html('');
                    openAiPromptArea.val('');
                },
                error: function (e) {
                    console.log(e)
                }
            });
        });

        openAiContentPublish.on('click', function () {
            let title = openAiTitle.html();
            let content = openAiContent.html();

            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "publish_post",
                    title: title,
                    content: content,
                },
                success: function (response) {
                    console.log(response);
                    openAiContentWrapper.attr("hidden", true);
                    openAiTitle.html('');
                    openAiContent.html('');
                    openAiPromptArea.val('');
                },
                error: function (e) {
                    console.log(e)
                }
            });
        })
    });
})(jQuery);
