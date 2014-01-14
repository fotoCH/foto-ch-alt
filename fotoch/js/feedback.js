/*
 * Slideout feedback form
 */
 (function( $j ) {
   feedback_button = {
      language: 'de',
      arrTranslation: {
         de: {
            email: "Ihre Email-Adresse (optional)",
            feedback: "Verbesserungsvorschläge, Probleme...",
            send: "Senden",
            confirmation: "Danke für das Feedback",
            error: "Es ist ein Problem aufgetreten. Bitte versuchen Sie es erneut."
         },
         fr: {
            email: "Votre adresse email (facultatif)",
            feedback: "Propositions d'améliorations, problèmes...",
            send: "Envoyer",
            confirmation: "Merci pour votre commentaire",
            error: "Erreur. Essayer de nouveau."
         },
         it: {
            email: "Il tuo indirizzo e-mail (facoltativo)",
            feedback: "Miglioramento suggerimenti, problemi",
            send: "Inviare",
            confirmation: "Grazie per il feedback",
            error: "C'è un problema. Riprova."
         },
         en: {
            email: "Your e-mail (optional)",
            feedback: "Suggestions, problems...",
            send: "Send",
            confirmation: "Thanks for your feedback",
            error: "Oups, an error occured. Please try again."
         },
      },
      onReady: function() {
         // request language from server and insert markup
         $j.ajax({
            type: "POST",
            url: "./ajax.php?action=getLang",
            success: function( language ) {
               feedback_button.insertMarkup(language);
               feedback_button.language = language;
            },
            error: function( result, status ) {
               // use German language strings if language can't be detected
               feedback_button.insertMarkup('de');
            }
         });
      },
      insertMarkup: function( language ) {
         $j('body').append('<div class="feedback">'+
            '<a id="feedback_button">Feedback</a>'+
            '<div class="form animated fadeOut">'+
               '<div class="status animated"></div>'+
               '<input id="sender_mail" type="email" name="email" placeholder="'+this.arrTranslation[language]['email']+'">'+
               '<textarea id="feedback_text" placeholder="'+this.arrTranslation[language]['feedback']+'"></textarea>'+
               '<input type="submit" value="'+this.arrTranslation[language]['send']+'" id="submit_form">'+
            '</div>'+
         '</div>');

         // set event handler
         this.feedback_button_handler();
         this.send_feedback();

         // detect mouse click outside feedback block
         $j(document).mouseup(function (e) {
            var feedbackBox = $j(".feedback");

            if (!feedbackBox.is(e.target) && feedbackBox.has(e.target).length === 0) {
               feedbackBox.removeClass('active');
               feedbackBox.children('.form').removeClass('fadeIn').addClass('fadeOut');
            }
         });

      },
      feedback_button_handler: function() {
         $j("#feedback_button").click(function() {
            $j(this).parent().toggleClass('active');
            if ($j(this).parent().hasClass('active')) {
               $j(this).next().addClass('fadeIn').removeClass('fadeOut');
            }
            else {
               $j(this).next().removeClass('fadeIn').addClass('fadeOut');
            }
            
         });
      },
      send_feedback: function() {
         $j("#submit_form").click(function() {
            if($j("#feedback_text").val() !== "") {
               $j('.status').text("");

               $j.ajax({
                  type: "POST",
                  url: "./ajax.php?action=sendFeedback",
                  data: 'feedback=' + $j('#feedback_text').val() + '&sender=' + $j('#sender_mail').val(),
                  success: function( result, status ) {
                     //email sent successfully displays a success message
                     if( result == 'Message Sent' ) {
                        $j('.form input, .form textarea').addClass('inactive');
                        $j('.status').text(feedback_button.arrTranslation[feedback_button.language]['confirmation']);
                        $j('.status').addClass('fadeIn');
                     }
                     else {
                        $j('.status').text(feedback_button.arrTranslation[feedback_button.language]['error']);
                     }
                  },
                  error: function( result, status ) {
                     $j('.status').text(feedback_button.arrTranslation[feedback_button.language]['error']);
                  }
               });
            }
         });
      },
   
   };

   $j().ready(function() {
      feedback_button.onReady();
   });

 })(jQuery);