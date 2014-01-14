/**
 * Grid layout for the Panel interface
 *
 * @copyright Greg Priday 2013
 * @license GPL 2.0 http://www.gnu.org/licenses/gpl-2.0.html
 */

(function($){
    
    // The initial values
    var gridId = 0;
    var cellId = 0;

    /**
     * Visually resize all the cells, this should happen after a new panel is added.
     */
    $.fn.panelsResizeCells = function(){

        return $(this ).each(function(){
            var $$ = $(this);

            $$.find( '.grid, .grid .cell, .grid .cell .cell-wrapper' ).css( 'height', 'auto' );
            var totalWidth = $$.find( '.grid' ).outerWidth();
            
            if ( $$.find( '.grid .cell' ).length > 1 ) {
                $$.find( '.grid .cell' ).each( function () {
                    if ( $( this ).is( '.first, .last' ) ) totalWidth -= 6;
                    else totalWidth -= 12;
                } );
            }

            var left = 0;
            var maxHeight = 0;
            $$.find( '.grid .cell' ).each( function () {
                maxHeight = Math.max( maxHeight,
                    // The height of a panel is 54 (49 height with 5 bottom margin) and an extra 14 for top and bottom cell-wrapper paddings
                    //($(this).find('.panel').length * 54) + 14
                    $(this ).height()
                );
                
                $( this )
                    .width( Math.floor( totalWidth * Number( $( this ).attr( 'data-percent' ) ) ) )
                    .css( 'left', left );
                left += $( this ).width() + 12;
            } );

            // Resize all the grids and cell wrappers
            $$.find( '.grid, .grid .cell, .grid .cell .cell-wrapper' ).css( 'height', Math.max( maxHeight, 68 ) );
        });

    }

    /**
     * Create a new grid
     *
     * @param cells
     * @param weights
     *
     * @return {*}
     */
    panels.createGrid = function ( cells, weights, style ) {
        if ( weights == null || weights.length == 0 ) {
            weights = [];
            for ( var i = 0; i < cells; i++ ) {
                weights[i] = 1;
            }
        }

        if(typeof style == 'undefined') style = '';

        var weightSum = 0;
        for (var index in weights) {
            weightSum += weights[index];
        }

        // Create a new grid container
        var container = $( '<div />' ).addClass( 'grid-container' ).appendTo( '#panels-container' );
        // Add the hidden field to store the grid order
        container.append( $( '<input type="hidden" name="grids[' + gridId + '][cells]" />' ).val( cells ) );

        var styleInput = $( '<input type="hidden" name="grids[' + gridId + '][style]" />' ).val(style).appendTo(container);

        container
            .append(
                $( '<div class="controls" />' )
                    // Add the remove button
                    .append(
                        $( '<div class="ui-button ui-button-icon-only"><div class="ui-icon ui-icon-remove"></div></div>' )
                            .attr( 'data-tooltip', panels.i10n.buttons['delete'] )
                            .click( function () {
                                $( this ).removeTooltip();

                                // Create an array that represents this grid
                                var containerData = [];
                                container.find('.cell' ).each(function(i, el){
                                    containerData[i] = {
                                        'weight' : Number($(this ).attr('data-percent')),
                                        'widgets' : []
                                    };
                                    $(this ).find('.panel' ).each(function(j, el){
                                        containerData[i]['widgets'][j] = {
                                            type : $(this ).attr('data-type'),
                                            data : $(this ).getPanelData()
                                        }
                                    })
                                });

                                // Register this with the undo manager
                                window.panels.undoManager.register(
                                    this,
                                    function(containerData, position){
                                        // Readd the grid
                                        var weights = [];
                                        for(var i = 0; i < containerData.length; i++){
                                            weights[i] = containerData[i].weight;
                                        }

                                        var gridContainer = window.panels.createGrid( weights.length, weights );

                                        // Now, start adding the widgets
                                        for(var i = 0; i < containerData.length; i++){
                                            for(var j = 0; j < containerData[i].widgets.length; j++){
                                                // Readd the panel
                                                var theWidget = containerData[i].widgets[j];
                                                var panel = $('#panels-dialog').panelsCreatePanel(theWidget.type, theWidget.data);
                                                window.panels.addPanel(panel, gridContainer.find('.panels-container' ).eq(i));
                                            }
                                        }

                                        // Finally, reposition the gridContainer
                                        if(position != gridContainer.index()){
                                            var current = $('#panels-container .grid-container' ).eq(position);
                                            if(current.length){
                                                gridContainer.insertBefore(current);
                                                $( '#panels-container' ).sortable( "refresh" )
                                                $( '#panels-container' ).find( '.cell' ).each( function () {
                                                    // Store which grid this is in by finding the index of the closest .grid-container
                                                    $( this ).find( 'input[name$="[grid]"]' ).val( $( '#panels-container .grid-container' ).index( $( this ).closest( '.grid-container' ) ) );
                                                } );

                                                $( '#panels-container .panels-container' ).trigger( 'refreshcells' );
                                            }
                                        }

                                        // We don't want to animate the new widgets
                                        $( '#panels-container .panel' ).removeClass( 'new-panel' );

                                        if(panels.animations) gridContainer.hide().slideDown();
                                        else gridContainer.show();

                                    },
                                    [containerData, container.index()],
                                    'Remove Columns'
                                );

                                // Create the undo notification
                                $('#panels-undo-message' ).remove();
                                $('<div id="panels-undo-message" class="updated"><p>' + panels.i10n.messages.deleteColumns + ' - <a href="#" class="undo">' + panels.i10n.buttons.undo + '</a></p></div>' )
                                    .appendTo('body')
                                    .hide()
                                    .fadeIn()
                                    .find('a.undo')
                                    .click(function(){
                                        window.panels.undoManager.undo();
                                        $('#panels-undo-message' ).fadeOut(function(){ $( this ).remove() });
                                        return false;
                                    })
                                ;

                                // Finally, remove the grid container
                                var remove = function () {
                                    // Remove all the panel dialogs
                                    container.find('.panel').each(function(){
                                        $(this).data('dialog').dialog('destroy').remove();
                                    });
                                    // Remove the container
                                    container.remove();

                                    // Refresh everything
                                    $( '#panels-container' )
                                        .sortable( "refresh" )
                                        .find( '.panels-container' ).trigger( 'refreshcells' );
                                };

                                if(panels.animations) container.slideUp( remove );
                                else {
                                    container.hide();
                                    remove();
                                }

                                return false;
                            } )

                    )
                    // Add the button for selecting the row style
                    .append (
                        $( '<div class="ui-button ui-button-icon-only panels-visual-style"><div class="ui-icon ui-icon-gear"></div></div>' )
                            .attr( 'data-tooltip', 'Visual Style' )
                            .click( function(){
                                if($('#panels-row-style-select').is(':visible') && $('#panels-row-style-select').data('panels-row') == container){
                                    $('#panels-row-style-select').fadeOut();
                                    return;
                                }

                                // Display the dropdown form
                                $('#panels-row-style-select')
                                    .hide()
                                    .fadeIn()
                                    .css({
                                        top: container.position().top + 28,
                                        right: -1
                                    })
                                    .data('panels-row', container)
                                    .find('li').removeClass('selected').unbind('click').click(function(){
                                        var $$ = $(this).addClass('selected');
                                        styleInput.val($$.data('value'));
                                        $('#panels-row-style-select').fadeOut();
                                    }).filter('[data-value="'+styleInput.val()+'"]').addClass('selected');
                            } )
                    )
                    // Add the move/reorder button
                    .append(
                        $( '<div class="ui-button ui-button-icon-only grid-handle"><div class="ui-icon ui-icon-move"></div></div>' )
                    )
            );

        // Hide the row style button if none are available
        if( $('#panels-row-style-select li').length == 1 ) {
            container.find('.panels-visual-style').remove();
        }

        var grid = $( '<div />' ).addClass( 'grid' ).appendTo( container );

        for ( var i = 0; i < cells; i++ ) {
            var cell = $(
                '<div class="cell" data-percent="' + (weights[i] / weightSum) + '">' +
                    '<div class="cell-wrapper panels-container"></div>' +
                    '<div class="cell-width"><div class="cell-width-left"></div><div class="cell-width-right"></div><div class="cell-width-line"></div><div class="cell-width-value"><span></span></div></div>' +
                    '</div>'
            );
            if ( i == 0 ) cell.addClass( 'first' );
            if ( i == cells - 1 ) cell.addClass( 'last' );
            grid.append( cell );

            // Add the cell information fields
            cell
                .append( $( '<input type="hidden" name="grid_cells[' + cellId + '][weight]" />' ).val( weights[i] / weightSum ) )
                .append( $( '<input type="hidden" name="grid_cells[' + cellId + '][grid]" />' ).val( gridId ) )
                .data( 'cellId', cellId )

            cellId++;
        }
        grid.append( $( '<div />' ).addClass( 'clear' ) );
        gridId++;

        // Setup the grid
        panels.setupGrid(container);

        return container;
    }

    /**
     * Setup a grid container after its been created.
     *
     * @param $$
     */
    panels.setupGrid = function ( $$ ) {
        // Hide the undo message
        $('#panels-undo-message' ).fadeOut(function(){ $(this ).remove() });

        $$.panelsResizeCells();

        $$.find( '.grid .cell' ).not( '.first' ).each( function () {
            var sharedCellWidth, sharedCellLeft;

            $( this ).resizable( {
                handles:    'w',
                containment:'parent',
                start:      function ( event, ui ) {
                    sharedCellWidth = $( this ).prev().outerWidth();
                    sharedCellLeft = $( this ).prev().position().left;
                },
                stop:       function ( event, ui ) {
                    $$.find( '.grid .cell' ).not( '.first' ).resizable( 'disable' ).resizable( 'enable' );
                },
                resize:     function ( event, ui ) {
                    var c = $( this );
                    var p = $( this ).prev();

                    p.css( 'width', c.position().left - p.position().left - 12 );

                    var totalWidth = 0;
                    $$.find( '.grid .cell' )
                        .each( function () {
                            totalWidth += $( this ).width();
                        } )
                        .each( function () {
                            var percent = $( this ).width() / totalWidth;
                            $( this ).find( '.cell-width-value span' ).html( Math.round( percent * 1000 ) / 10 + '%' );
                            $( this ).attr( 'data-percent', percent ).find( 'input[name$="[weight]"]' ).val( percent );
                        } );

                    $$.panelsResizeCells();
                }
            } );
        } );

        // Enable double clicking on the resizer
        $$.find( '.grid .cell .ui-resizable-handle' ).dblclick( function () {
            var c1 = $( this ).closest( '.cell' );
            var c2 = c1.prev();
            var totalPercent = Number( c1.attr( 'data-percent' ) ) + Number( c2.attr( 'data-percent' ) );
            c1.attr( 'data-percent', totalPercent / 2 ).find( 'input[name$="[weight]"]' ).val( totalPercent / 2 );
            c2.attr( 'data-percent', totalPercent / 2 ).find( 'input[name$="[weight]"]' ).val( totalPercent / 2 );
            c1.add( c2 ).find( '.cell-width-value span' ).html( Math.round( totalPercent / 2 * 1000 ) / 10 + '%' );
            $$.panelsResizeCells();

            return false;
        } );

        $$.find( '.grid .cell' )
            .click(function(){
                $( '.grid .cell' ).removeClass('cell-selected');
                $(this ).addClass('cell-selected');
            })
            .each( function () {
                var percent = Number( $( this ).attr( 'data-percent' ) );
                $( this ).find( '.cell-width-value span' ).html( Math.round( percent * 1000 ) / 10 + '%' );
            } )
            .find( '.panels-container' )
            // This sortable handles the widgets inside the cell
            .sortable( {
                placeholder:"ui-state-highlight",
                connectWith:".panels-container",
                tolerance:  'pointer',
                change:     function (ui) {
                    var thisContainer = $('#panels-container .ui-state-highlight' ).closest('.cell' ).get(0);
                    if(typeof this.lastContainer != 'undefined' && this.lastContainer != thisContainer){
                        // Resize the new and the last containers
                        $(this.lastContainer ).closest('.grid-container').panelsResizeCells();
                        $(thisContainer).closest('.grid-container').panelsResizeCells();
                        thisContainer.click();
                    }

                    // Refresh all the cell sizes after we stop sorting
                    this.lastContainer = thisContainer;
                },
                helper: function(e, el){
                    return el.clone().css('opacity', panels.animations ? 0.9 : 1).addClass('panel-being-dragged');
                },
                stop:       function (ui, el) {
                    // Refresh all the cell sizes after we stop sorting
                    $( '#panels-container .grid-container' ).each( function () {
                        $(this).panelsResizeCells();
                    } );
                },
                receive:    function () {
                    $( this ).trigger( 'refreshcells' );
                }
            } )
            .bind( 'refreshcells', function () {
                // Set the cell for each panel
                // Refresh all the cell sizes after we stop sorting
                $( '#panels-container .grid-container' ).each( function () {
                    $(this).panelsResizeCells(true);
                } );

                $( '#panels-container .panel' ).each( function () {
                    var container = $( this ).closest( '.grid-container' );

                    $( this).data('dialog').find( 'input[name$="[info][grid]"]' ).val( $( '#panels-container .grid-container' ).index( container ) );
                    $( this ).data('dialog').find( 'input[name$="[info][cell]"]' ).val( container.find( '.cell' ).index( $( this ).closest( '.cell' ) ) );
                } );

                $( '#panels-container .cell' ).each( function () {
                    $( this ).find( 'input[name$="[grid]"]' ).val( $( '#panels-container .grid-container' ).index( $( this ).closest( '.grid-container' ) ) );
                } );
            } )
            .disableSelection();
    }

    /**
     * Clears all the grids
     */
    panels.clearGrids = function(){
        $('#panels-container .grid-container' ).remove();
    }
    
})(jQuery);