<div id="translation-entry"
data-module="translation"
>
{* iterate through all translations where the key of the array defines the associated language. The counter 
   uniquely identifies each translation entry, so that the user might levelage this information while navigating
   the results *}
{counter start=-1 print=false}
<div class="row">
  {foreach from=$translations key=language item=translationsForLanguage}
  <article class="col-sm-{$maxColumnWidth} col-md-{$midColumnWidth} col-lg-{$minColumnWidth}">
    <header>
      <h2 rel="language-box">
      {$language} 
      {if isset($languages[$language]) && !is_null($languages[$language]->tengwar)}
      <span class="tengwar">{$languages[$language]->tengwar}</span>
      {/if}
      </h2>
    </header>
    <section class="language-box" id="language-box-{$language}">
    {* Iterate through each entry for the specificed language *}
    {foreach $translationsForLanguage as $translation}
    <blockquote itemscope="itemscope" itemtype="http://schema.org/Article" id="translation-block-{counter}" {if !$translation->group->canon}class="contribution"{/if}>
      <img class="hidden" itemprop="image" src="http://www.elfdict.com/img/favicons/mstile-310x310.png"> {* Please Google... *}
      <h3 rel="trans-word" class="trans-word">
        {if !$translation->group->canon || $translation->uncertain}
        <a href="about.page?browseTo=unverified" title="Unverified or debatable content."><span class="glyphicon glyphicon-question-sign"></span></a>
        {/if}
        <span itemprop="headline">{$translation->word}</span>
        {if $loggedIn}
          {if $isAdmin}
            <a href="#" class="ed-delete-button" data-translation-id="{$translation->id}" title="Delete this item"><span class="glyphicon glyphicon-trash pull-right" aria-hidden="true"></span></a>
            <a href="translate-form.page?translationID={$translation->id}" title="Edit this item"><span class="glyphicon glyphicon-pencil pull-right" aria-hidden="true"></span></a>
          {/if}
          <a href="#" class="ed-favourite-button" data-translation-id="{$translation->id}" title="Add to favourites"><span class="glyphicon glyphicon-heart{if !in_array($translation->id, $favourites)}-empty{/if} pull-right" aria-hidden="true"></span></a>
        {/if}
        {if $translation->group->externalLinkFormat !== null && $translation->externalID !== null}
          {assign var="externalLink" value=$translation->group->externalLinkFormat}
          <a href="{str_replace('{ExternalID}', $translation->externalID, $externalLink)}" class="ed-external-link-button" title="Open on {$translation->group->name} (new tab/window)" target="_blank"><span class="glyphicon glyphicon-globe pull-right" aria-hidden="true"></span></a>
        {/if}
      </h3> 
      {if $translation->tengwar != null}
      &#32;<span class="tengwar">{$translation->tengwar}</span>
      {elseif $language eq 'Noldorin' or $language eq 'Sindarin'}
      {/if}
      {if $translation->type != 'unset'}<span class="word-type" rel="trans-type">{$translation->type}.</span>{/if}
      <span rel="trans-translation" itemprop="keywords">{$translation->translation}</span>

      <p class="word-comments" rel="trans-comments" itemprop="articleBody">{nl2br(trim($translation->comments))}</p>

      {if !$translation->group->canon}
      <section class="alert alert-warning" itemprop="comment">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        Unverified or debatable content.
      </section>
      {/if}

      {* Only bother with references if such are defined, as they are put within brackets *}
      <footer>
        {if $translation->source != null}<span class="word-source" rel="trans-source">[{$translation->source}]</span>{/if}

        {if !empty($translation->etymology)}
        <span class="word-etymology" rel="trans-etymology">{$translation->etymology}.</span>
        {/if}
        {if $translation->group->id}
          Group: <span itemprop="sourceOrganization">{$translation->group->name}</span>.
        {/if}
        Published <span itemprop="datePublished">{$translation->dateCreated}</span> by <a href="/profile.page?authorID={$translation->authorID}" itemprop="author" rel="author" title="View profile for {$translation->authorName}.">{$translation->authorName}</a>
      </footer>
    </blockquote>
    {/foreach}
    </section>
  </article>
  {/foreach}
</div>

{* Show a message if no such word exists *}
{if $senses|@count < 1}
<div class="row">
  <h3>Forsooth! I can't find what you're looking for!</h3>
  <p>The word <em>{$term}</em> hasn't been recorded for any of the languages.</p>
</div>
{/if}

</div>
