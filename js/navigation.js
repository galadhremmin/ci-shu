define(['exports', 'utilities'], function (exports, util) {
  /**
   * Grabs the hash on change, and passes it to the translation service. 
   *
   * @class CNavigator
   * @constructor
   * @param {String} containerId Navigation result container.
   */
  var CNavigator = function (containerId) { 
    util.CAssert.string(containerId);
    
    this.currentTerm = undefined;
    this.containerId = containerId;
  }
  
  /**
   * Binds the hash change event to the window, and processes existing
   * hashes. This method should only be invoked once.
   *
   * @method listen
   */
  CNavigator.prototype.listen = function ()  {
    var _this = this;
    var currentHash;
    
    $(window).on('hashchange', function () {
      _this.navigate( String(window.location.hash).substr(1) );
    });
    
    this.navigate(String(window.location.hash).substr(1));
  }
  
  /**
   * Navigates to the specified hash. This method is invoked when the
   * hash changes.
   *
   * @method navigate
   * @param {String} hash Hash argument in its raw form (location.hash).
   */
  CNavigator.prototype.navigate = function (hash) {
    util.CAssert.string(hash);
    
    var term = $.trim(hash);
    if (term.length < 1) {
      return;
    }
    
    if (term.indexOf('%') > -1) {
      term = decodeURIComponent(term);
    }
    
    var _this = this;
    $.get('translate.php', { term: term, ajax: true }, function (data) {
      _this.navigated(data);
      _this.currentTerm = term;
    });
  }
  
  /**
   * Handles the specified data string once it has been successfully 
   * received by AJAX. This method is triggered by the method navigate.
   * This method changes the DOM.
   *
   * @method navigate
   * @param {String} data Data from the AJAX request.
   */
  CNavigator.prototype.navigated = function (data) {
    util.CAssert.string(data);
    
    var result = document.getElementById(this.containerId);
    if (result) {
      result.innerHTML = data;
    }
  }
  
  exports.CNavigator = CNavigator;
  
  /**
   * Handles search suggestions and navigation through the search 
   * results.
   *
   * @class CSearchNavigator
   * @constructor
   * @param {String} searchFieldId    ID for a search field.
   * @param {String} searchResultId   ID for a search result field.
   * @param {String} searchResultId   ID for a reverse query checkbox.
   * @param {String} languageFilterId ID for a select box with languages.
   */
  var CSearchNavigator = function (searchFieldId, searchResultId, 
    reversedSearchId, languageFilterId) {
    util.CAssert.string(searchFieldId, searchResultId, reversedSearchId);
    
    this.searchFieldId    = searchFieldId;
    this.searchResultId   = searchResultId;
    this.reversedSearchId = reversedSearchId;
    this.languageFilterId = languageFilterId;
    
    this.searchField      = null;
    this.resultContainer  = null;
    this.resultWrapper    = null;
    this.resultCountLabel = null;
    
    this.currentDigest    = 0;
    this.changeTimeout    = 0;
    this.languageId       = 0;
    
    this.isReversed       = false;
  }
  
  /**
   * Binds the change event to the search field, and processes existing
   * searches. This method should only be invoked once.
   *
   * @method listen
   */
  CSearchNavigator.prototype.listen = function ()  {
    var _this = this;
    var currentHash;
    
    // Find all result container. References to them are retained for 
    // performance reasons.
    this.searchField      = document.getElementById(this.searchFieldId);
    this.resultContainer  = document.getElementById(this.searchResultId);
    this.resultWrapper    = document.getElementById(this.searchResultId + '-wrapper');
    this.resultCountLabel = document.getElementById(this.searchResultId + '-count');
    
    // Attach events
    $(this.searchField).on('keyup', function (ev) {
      _this.beginSpringSuggestions($(this));
    });
    
    $('#' + this.languageFilterId).on('change', function () {
      var languageId = parseInt(this.options[this.selectedIndex].value);
      _this.changeLanguage(languageId);
    });
    
    $('#' + this.reversedSearchId).on('change', function () {
      var reversed = this.checked;
      _this.changeReversed(reversed);
    });
    
    // Free up resources no longer needed
    this.searchResultId   = undefined;
    this.searchFieldId    = undefined;
    this.languageFilterId = undefined;
    this.reversedSearchId = undefined;
  }
  
  /**
   * Event handler for language selection.
   *
   * @private
   * @method changeLanguage
   * @param {Number} id  New language ID
   */
  CSearchNavigator.prototype.changeLanguage = function (id) {
    util.CAssert.number(id);
    this.language = id;
    
    // Search conditions have changed! Request new suggestions.
    this.endSpringSuggestions();
  }
  
  /**
   * Event handler for reversed search selection.
   *
   * @private
   * @method changeLanguage
   * @param {Boolean} id  New reversed state.
   */
  CSearchNavigator.prototype.changeReversed = function (reversed) {
    util.CAssert.boolean(reversed);
    this.isReversed = reversed ? 1 : 0;
    
    // Search conditions have changed! Request new suggestions.
    this.endSpringSuggestions();
  }
  
  /**
   * Invokes the mehod endSpringSuggestions asynchronously, and erases previous
   * instances of the same method awaiting invocation. This timeout mechanism is
   * meant to throttle search queries while the client is still typing.
   *
   * @private
   * @method beginSpringSuggestions
   */
  CSearchNavigator.prototype.beginSpringSuggestions = function () {        
    if (this.changeTimeout) {
      window.clearTimeout(this.changeTimeout);
    }
    
    var _this = this;
    this.changeTimeout = window.setTimeout(function () {
      _this.changeTimeout = 0;
      _this.endSpringSuggestions();
    }, 250);
  }
    
  /**
   * Invokes springSuggestions provided that the element's value digest differ
   * from the digest for the loaded suggestions.
   *
   * @private
   * @method endSpringSuggestions
   */
  CSearchNavigator.prototype.endSpringSuggestions = function () {
    var value = this.searchField.value,
        digest = (value + ',' + this.isReversed + ',' + this.language).hashCode(),
        term;
    
    if (digest === this.currentDigest) {
      // No change, so return!
      return;
    }
    
    term = $.trim(value);
    if (term.length < 1) {
      return;
    }
    
    this.requestSuggestions(term);
    this.currentDigest = digest;
  }
   
  /**
   * Retrieves suggestions for the specified term through an AJAX request to 
   * the web service.
   *
   * @private
   * @method requestSuggestions
   * @param {String} term  Term to retrieve suggestions for.
   */
  CSearchNavigator.prototype.requestSuggestions = function (term) {
    util.CAssert.string(term);
    
    var requestData = {
      'term': term,
      'reversed': this.isReversed || 0,
      'language-filter': this.language || 0
    };
    
    var _this = this;
    
    $.ajax({
      url: 'api/word/search',
      data: requestData,
      dataType: 'json',
      type: 'post',
      success: function(data) {
        if (data.succeeded) {
          _this.presentSuggestions(data.response.words);
        }
      }
    });
  }
  
  /**
   * Presents the provided array with suggestions. This method assumes that the 
   * array contains objects with at least two properties, nkey and key.
   *
   * @private
   * @method presentSuggestions
   * @param {String} suggestions  An array with objects containing key and nkey.
   */
  CSearchNavigator.prototype.presentSuggestions = function (suggestions) {
    util.CAssert.array(suggestions);
    
    var items = [], suggestion, i;
    
    for (i = 0; i < suggestions.length; i += 1) {
      suggestion = suggestions[i];
      items.push('<li><a class="search-result-item" href="#' + encodeURIComponent(suggestion.nkey) + '">' + suggestion.key + '</a></li>');
    }
    
    // Open/close the wrapper depending on the result set.
    if (items.length > 0) {
      $(this.resultWrapper).removeClass('hidden');
    
      // Wrap the items in <ul> tags and and update the result container
      items.unshift('<ul>');
      items.push('</ul>');
      
      this.resultContainer.innerHTML = items.join('');
    } else {
      $(this.resultWrapper).addClass('hidden');
    }
    
    if (this.resultCountLabel) {
      this.resultCountLabel.innerText = items.length;
    }
  }
  
  exports.CSearchNavigator = CSearchNavigator;
});
