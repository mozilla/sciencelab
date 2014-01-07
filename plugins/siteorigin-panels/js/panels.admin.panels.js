/**
 * Main admin control for the Panel interface
 *
 * @copyright Greg Priday 2013
 * @license GPL 2.0 http://www.gnu.org/licenses/gpl-2.0.html
 */

(function($){

    var newPanelId = 0;

    panels.undoManager = new UndoManager();
    
    /**
     * A jQuery function to get panels data
     */
    $.fn.getPanelData = function(){
        var $$ = $(this);
        var data = {};

        $$.data('dialog').find( '*[name]' ).not( '[data-info-field]' ).each( function () {
            var name = /widgets\[[0-9]+\]\[([^\]]+)\]/.exec($(this).attr('name'));
            name = name[1];
            data[name] = $( this ).val();
        } );

        return data;
    }

    /**
     * Create and return a new panel
     *
     * @param type
     * @param data
     *
     * @return {*}
     */
    $.fn.panelsCreatePanel = function ( type, data ) {
        newPanelId++;

        var dialogWrapper = $( this );
        var $$ = dialogWrapper.find('.panel-type' ).filter(function() { return $(this).data('class') === type });

        if($$.length == 0) return null;

        // Hide the undo message
        $('#panels-undo-message' ).fadeOut(function(){ $(this ).remove() });
        var panel = $( '<div class="panel new-panel"><div class="panel-wrapper"><div class="title"><h4></h4><span class="actions"></span></div><small class="description"></small></div></div>' )
            .attr('data-type', type);

        var dialog;

        var formHtml = $$.attr( 'data-form' );
        formHtml = formHtml.replace( /\{\$id\}/g, newPanelId );

        panel
            .data( {
                // We need this data to update the title
                'title-field': $$.attr( 'data-title-field' ),
                'title':       $$.attr( 'data-title' )
            } )
            .find( 'h4, h5' ).click( function () {
                dialog.dialog( 'open' );
                return false;
            } )
            .end().find( '.description' ).html( $$.find( '.description' ).html() );

        // Create the dialog buttons
        var dialogButtons = {};
        // The delete button
        var deleteFunction = function () {
            // Add an entry to the undo manager
            panels.undoManager.register(
                this,
                function(type, data, container, position){
                    // Readd the panel
                    var panel = $('#panels-dialog').panelsCreatePanel(type, data, container);
                    panels.addPanel(panel, container, position, true);

                    // We don't want to animate the undone panels
                    $( '#panels-container .panel' ).removeClass( 'new-panel' );
                },
                [panel.attr('data-type'), panel.getPanelData(), panel.closest('.panels-container'), panel.index()],
                'Remove Panel'
            );

            // Create the undo notification
            $('#panels-undo-message' ).remove();
            $('<div id="panels-undo-message" class="updated"><p>' + panels.i10n.messages.deleteWidget + ' - <a href="#" class="undo">' + panels.i10n.buttons.undo + '</a></p></div>' )
                .appendTo('body')
                .hide()
                .slideDown()
                .find('a.undo')
                .click(function(){
                    panels.undoManager.undo();
                    $('#panels-undo-message' ).fadeOut(function(){ $(this ).remove() });
                    return false;
                })
            ;

            var remove = function () {
                // Remove the dialog too
                panel.data('dialog').dialog('destroy').remove();
                panel.remove();
                $( '#panels-container .panels-container' ).trigger( 'refreshcells' );
            };

            if(panels.animations) panel.slideUp( remove );
            else {
                panel.hide();
                remove();
            }
            dialog.dialog( 'close' );
        };

        // The done button
        dialogButtons[panels.i10n.buttons['done']] = function () {
            $( this ).trigger( 'panelsdone', panel, dialog );

            // Change the title of the panel
            panel.panelsSetPanelTitle();
            dialog.dialog( 'close' );
        }

        dialog = $( '<div class="panel-dialog dialog-form"></div>' )
            .addClass('widget-dialog-' + type.toLowerCase())
            .html( formHtml )
            .dialog( {
                dialogClass: 'panels-admin-dialog',
                autoOpen:    false,
                modal:       false, // Disable modal so we don't mess with media editor. We'll create our own overlay.
                draggable:   false,
                resizable:   false,
                title:       panels.i10n.messages.editWidget.replace( '%s', $$.attr( 'data-title' ) ),
                minWidth:    760,
                maxHeight:   Math.round($(window).height() * 0.925),
                create:      function(event, ui){
                    $(this ).closest('.ui-dialog' ).find('.show-in-panels' ).show();
                },
                open:        function () {
                    // This gives panel types a chance to influence the form
                    $( this ).trigger( 'panelsopen', panel, dialog );

                    // This fixes a weird a focus issue
                    $(this ).closest('.ui-dialog' ).find('a' ).blur();

                    var overlay = $('<div class="ui-widget-overlay ui-front"></div>').css('z-index', 1000);
                    $(this).data('overlay', overlay).closest('.ui-dialog').before(overlay);
                },
                close: function(){
                    $(this).data('overlay').remove();
                },
                buttons:     dialogButtons
            } )
            .keypress(function(e) {
                if (e.keyCode == $.ui.keyCode.ENTER) {
                    if($(this ).closest('.ui-dialog' ).find('textarea:focus' ).length > 0) return;

                    // This is the same as clicking the add button
                    $(this ).closest('.ui-dialog').find('.ui-dialog-buttonpane .ui-button:eq(0)').click();
                    e.preventDefault();
                    return false;
                }
                else if (e.keyCode === $.ui.keyCode.ESCAPE) {
                    $(this ).closest('.ui-dialog' ).dialog('close');
                }
            });

        // This is so we can access the dialog (and its forms) later.
        panel.data('dialog', dialog).disableSelection();

        // Add the action buttons
        panel.find('.title .actions')
            .append(
                $('<a>' + panels.i10n.buttons.edit + '<a>' ).addClass('edit' ).click(function(){
                    dialog.dialog('open');
                    return false;
                })
            )
            .append(
                $('<a>' + panels.i10n.buttons.duplicate + '<a>' ).addClass('duplicate' ).click(function(){
                    // Duplicate the widget
                    var duplicatePanel = $('#panels-dialog').panelsCreatePanel(panel.attr('data-type'), panel.getPanelData());
                    window.panels.addPanel(duplicatePanel, panel.closest('.panels-container'), null, false);
                    duplicatePanel.removeClass('new-panel');

                    return false;
                })
            )
            .append(
                $('<a>' + panels.i10n.buttons.delete + '<a>' ).addClass('delete').click(function(){
                    deleteFunction();
                    return false;
                })
            );

        if ( data != undefined ) {
            // Populate the form values
            for ( c in data ) {
                if ( c != 'info' ) {
                    var de = dialog.find( '*[name$="[' + c + ']"]' );

                    if ( de.attr( 'type' ) == 'checkbox' ) {
                        de.prop( 'checked', Boolean( data[c] ) );
                    }
                    else {
                        de.val( data[c] );
                    }
                }
            }
        }

        panel.panelsSetPanelTitle();

        // This is to refresh the dialog positions
        $( window ).resize();
        $(document).trigger('panelssetup', panel, dialog);

        return panel;
    }

    /**
     * Add a new Panel (well, widget)
     *
     * @param panel The new panel (Widget) we're adding.
     * @param container The container we're adding it to
     * @param position The position
     * @param booll animate Should we animate the panel
     */
    panels.addPanel = function(panel, container, position, animate){
        if(container == null) container = $( '#panels-container .cell.cell-selected .panels-container' ).eq(0);
        if(container.length == 0) container = $( '#panels-container .cell .panels-container' ).eq(0);
        if(container.length == 0) return;

        if (position == null) container.append( panel );
        else {
            var current = container.find('.panel' ).eq(position);
            if(current.length == 0) container.append( panel );
            else {
                panel.insertBefore(current);
            }
        }

        container.sortable( "refresh" ).trigger( 'refreshcells' );
        container.closest( '.grid-container' ).panelsResizeCells();
        if(animate) {
            if(panels.animations)
                $( '#panels-container .panel.new-panel' ).hide().slideDown( 500 , function(){ panel.data('dialog' ).dialog('open') } ).removeClass( 'new-panel' );
            else {
                $( '#panels-container .panel.new-panel').show().removeClass( 'new-panel' );
                setTimeout(function(){
                    panel.data('dialog' ).dialog('open');
                }, 500);
            }
        }
    }

    /**
     * Set the title of the panel
     */
    $.fn.panelsSetPanelTitle = function ( ) {
        return $(this ).each(function(){
            var titleValue = '';
            $(this).data('dialog').find( 'input[type="text"], textarea').each(function(){
                titleValue = $(this).val();
                if(titleValue != '') return false;
            });

            $(this ).find( 'h4' ).html( $(this ).data( 'title' ) + '<span>' + titleValue.substring(0, 80).replace(/(<([^>]+)>)/ig,"") + '</span>' );
        });
    }

    /**
     * Loads panel data
     *
     * @param data
     */
    panels.loadPanels = function(data){
        panels.clearGrids();

        // Create all the content
        for ( var gi in data.grids ) {
            var cellWeights = [];

            // Get the cell weights
            for ( var ci in data.grid_cells ) {
                if ( Number( data.grid_cells[ci]['grid'] ) == gi ) {
                    cellWeights[cellWeights.length] = Number( data.grid_cells[ci].weight );
                }
            }

            // Create the grids
            var grid = panels.createGrid( Number( data.grids[gi]['cells'] ), cellWeights, data.grids[gi]['style'] );

            // Add panels to the grid cells
            for ( var pi in data.widgets ) {

                if ( Number( data.widgets[pi]['info']['grid'] ) == gi ) {
                    var pd = data.widgets[pi];
                    var panel = $('#panels-dialog').panelsCreatePanel( pd['info']['class'], pd );
                    grid
                        .find( '.panels-container' ).eq( Number( data.widgets[pi]['info']['cell'] ) )
                        .append( panel )
                }
            }
        }

        $( '#panels-container .panels-container' )
            .sortable( 'refresh' )
            .trigger( 'refreshcells' );

        // Remove the new-panel class from any of these created panels
        $( '#panels-container .panel' ).removeClass( 'new-panel' );
        
        // Make sure everything is sized properly
        $( '#panels-container .grid-container' ).each( function () {
            $( this ).panelsResizeCells();
        } );
    }

})(jQuery);