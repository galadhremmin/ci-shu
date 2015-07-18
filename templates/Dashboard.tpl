<div data-module="dashboard">
  {if $message === 'review-updated'}
    <div class="alert alert-success" role="alert"><strong>Mae goren!</strong> Your suggestion has been successfully updated and resubmitted for review.</div>
  {elseif $message === 'review-created'}
    <div class="alert alert-info" role="alert"><strong>Mae goren!</strong> Your suggestion has been successfully submitted for review.</div>
  {elseif $message === 'review-rejected'}
    <div class="alert alert-info" role="alert"><strong>Alae i thíw egyl!</strong> The review item has been successfully rejected.</div>
  {elseif $message === 'review-approved'}
    <div class="alert alert-success" role="alert"><strong>Mae goren!</strong> The review item has been successfully approved.</div>
  {elseif $message === 'review-deleted'}
    <div class="alert alert-info" role="alert"><strong>Alae!</strong> The review item is no more.</div>
  {/if}

	<h2>Dashboard</h2>
	<p>Test.</p>
	
	<div class="row">

	  <div class="col-sm-6">
	    <h3>Favourites</h3>
	    <p></p>
	    <ul class="list-group">
	      {if count($favourites) < 1}
	      You have no favourite words just yet.
	      {else}
	      {foreach $favourites as $favourite}
	      <li class="list-group-item" id="favourite-{$favourite->id}">
	        <a href="/index.page#translationID={urlencode($favourite->translation->id)}">{$favourite->translation->word}</a>
	        <a class="pull-right favourite-delete" href="#" data-favourite-id="{$favourite->id}"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
	      </li>
	      {/foreach}
	      {/if}
	    </ul>
	  </div>
    <div class="col-sm-6">
      <h3>Flashcards</h3>
      <ul class="list-group">Coming soon!</ul>
    </div>
	</div>
	
	<div class="row">

    <div class="col-sm-6">
      <h3>Approved entries</h3>
      <p></p>
      <ul class="list-group">
        {if count($translations) < 1}
          There are no items in this list.
        {else}
          {foreach $translations as $translation}
            <li class="list-group-item" id="translation-{$translation->id}">
              <span class="ed-dashboard-li-text">
                <a href="/index.page#translationID={$translation->id}">{$translation->word}</a>
                &mdash;
                {$translation->translation}
              </span>
              <span class="label label-default pull-right">{date_format($translation->dateCreated, 'Y-m-d H:i')}</span>
            </li>
          {/foreach}
        {/if}
      </ul>
      <!--
      <nav>
        <ul class="pagination">
          <li>
            <a href="#" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">4</a></li>
          <li><a href="#">5</a></li>
          <li>
            <a href="#" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
        </ul>
      </nav>
      -->
      <a href="/translate-form.page " role="button" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Create</a>
    </div>

    {if null !== $reviews}
	  <div class="col-sm-6">
	    <h3>Contributions</h3>
      <ul class="list-group">
        {if count($reviews) < 1}
          There are no contributions awaiting to be reviewed by an administrator at this time.
        {else}
          {foreach $reviews as $review}
            <li class="list-group-item" id="review-{$review->reviewID}">
              {if $review->approved === true}
                {$review->word}
                <span class="label label-success pull-right">Approved</span>
              {elseif $review->approved === false}
                <s>{$review->word}</s>
                <a href="/translate-form.page?reviewID={$review->reviewID}">Correct and submit again</a>
                <span class="label label-danger pull-right" title="{htmlentities($review->justification)}">Rejected</span>
              {else}
                <a href="/translate-form.page?reviewID={$review->reviewID}">{$review->word}</a>
              {/if}
              <span class="label label-default pull-right">{date_format($review->dateCreated, 'Y-m-d H:i')}</span>
            </li>
          {/foreach}
        {/if}
      </ul>
	  </div>
	  {/if}
	</div>
</div>