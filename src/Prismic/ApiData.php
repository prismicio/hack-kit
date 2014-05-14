<?hh // strict

/*
 * This file is part of the Prismic hack SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic;

use Prismic\Form;

class ApiData
{
    private ImmVector<Ref> $refs;
    private ImmMap<string, string> $bookmarks;
    private ImmMap<string, string> $types;
    private ImmVector<string> $tags;
    private ImmMap<string, Form> $forms;
    private string $oauth_initiate;
    private string $oauth_token;

    /**
     * @param ImmVector $refs
     * @param ImmMap    $bookmarks
     * @param ImmMap    $types
     * @param ImmVector $tags
     * @param ImmMap    $forms
     * @param string    $oauth_initiate
     * @param string    $oauth_token
     */
    public function __construct(
        ImmVector<Ref> $refs,
        ImmMap<string, string> $bookmarks,
        ImmMap<string, string> $types,
        ImmVector<string> $tags,
        ImmMap<string, Form> $forms,
        string $oauth_initiate,
        string $oauth_token
    ) {
        $this->refs = $refs;
        $this->bookmarks = $bookmarks;
        $this->types = $types;
        $this->tags = $tags;
        $this->forms = $forms;
        $this->oauth_initiate = $oauth_initiate;
        $this->oauth_token = $oauth_token;
    }

    public function getRefs(): ImmVector<Ref>
    {
        return $this->refs;
    }

    public function getBookmarks(): ImmMap<string, string>
    {
        return $this->bookmarks;
    }

    public function getTypes(): ImmMap<string, string>
    {
        return $this->types;
    }

    public function getTags(): ImmVector<string>
    {
        return $this->tags;
    }

    public function getForms(): ImmMap<string, Form>
    {
        return $this->forms;
    }

    public function getOauthInitiate(): string
    {
        return $this->oauth_initiate;
    }

    public function getOauthToken(): string
    {
        return $this->oauth_token;
    }
}
