<?php

// Home
Breadcrumbs::register('home', function($breadcrumbs)
{
    $breadcrumbs->push('Home', route('home'));
});

// //////////////////////////////////////////////////////////////////////////////////////////////
// Phrases

Breadcrumbs::register('sentence.public', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Phrases', route('sentence.public'));
});

// Phrases > [Language]
Breadcrumbs::register('sentence.public.language', function($breadcrumbs, int $langId, string $langName)
{
    $link = new \App\Helpers\LinkHelper();

    $breadcrumbs->parent('sentence.public');
    $breadcrumbs->push($langName, $link->sentencesByLanguage($langId, $langName));
});

// Phrases > [Language] > [Phrase]
Breadcrumbs::register('sentence.public.sentence', function($breadcrumbs, int $langId, string $langName,
                                                 int $sentenceId, string $sentenceName)
{
    $link = new \App\Helpers\LinkHelper();

    $breadcrumbs->parent('sentence.public.language', $langId, $langName);
    $breadcrumbs->push($sentenceName, $link->sentence($langId, $langName, $sentenceId, $sentenceName));
});

// //////////////////////////////////////////////////////////////////////////////////////////////
// Dashboard

Breadcrumbs::register('dashboard', function ($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Dashboard', route('dashboard'));
});

// //////////////////////////////////////////////////////////////////////////////////////////////
// Dashboard > Speech

Breadcrumbs::register('speech.index', function ($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Type of speeches', route('speech.index'));
});

// Dashboard > Speech > Add speech
Breadcrumbs::register('speech.create', function ($breadcrumbs)
{
    $breadcrumbs->parent('speech.index');
    $breadcrumbs->push('Add type of speech', route('speech.create'));
});

// Dashboard > Speech > [Speech name]
Breadcrumbs::register('speech.edit', function ($breadcrumbs, App\Models\Speech $speech)
{
    $breadcrumbs->parent('speech.index');
    $breadcrumbs->push('Speech: '.$speech->Name, route('speech.edit', [ 'id' => $speech->SpeechID ]));
});

// //////////////////////////////////////////////////////////////////////////////////////////////
// Dashboard > Inflections

Breadcrumbs::register('inflection.index', function ($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Inflections', route('inflection.index'));
});

// Dashboard > Inflections > Add inflection
Breadcrumbs::register('inflection.create', function ($breadcrumbs)
{
    $breadcrumbs->parent('inflection.index');
    $breadcrumbs->push('Add inflection', route('inflection.create'));
});

// Dashboard > Inflections > Edit [Inflection]
Breadcrumbs::register('inflection.edit', function ($breadcrumbs, App\Models\Inflection $inflection)
{
    $breadcrumbs->parent('inflection.index');
    $breadcrumbs->push('Inflection: '.$inflection->Name, route('inflection.edit', [
        'id' => $inflection->InflectionID
    ]));
});

// //////////////////////////////////////////////////////////////////////////////////////////////
// Dashboard > Phrases

Breadcrumbs::register('sentence.index', function ($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Phrases', route('sentence.index'));
});

Breadcrumbs::register('sentence.create', function ($breadcrumbs)
{
    $breadcrumbs->parent('sentence.index');
    $breadcrumbs->push('Add phrase', route('sentence.create'));
});