<?php
namespace Robo\Task\Filesystem;

use Robo\Result;
use Robo\Task\StackBasedTask;
use Symfony\Component\Filesystem\Filesystem as sfFilesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * GitHub operations. Note that every operation in one GitHubStack must
 * operate on the same repository.  If altering multiple repositories, then
 * use multiple GitHub stacks.
 *
 * ``` php
 * <?php
 * $this->taskGitHubStack()
 *      ->repo('myrepo')
 *      ->owner('myorg')
 *      ->authToken($token)
 *      ->createRepository()
 *          ->description($description)
 *          ->homepage($homepage)
 *          ->isPrivate(true)
 *          ->hasIssues(true)
 *          ->hasWiki(false)
 *          ->hasDownloads(true)
 *          ->autoInit(true)
 *          ->licenseTemplate('mit')
 *      ->createPR($title, $base, $prBranch)
 *          ->body($body)
 *      ->createLabel($labelName, $color)
 *      ->addLabel($issueNumber, $labelName)
 *      ->run();
 * ?>
 * ```
 *
 */
class GitHubStack extends GitHub
{
    protected $stack = [];
    protected $allowedOptionalParameters = [];

    public function __construct()
    {
    }

    /**
     * Create a new repository owned by the authenticated user.
     */
    public function createUserRepository()
    {
        $this->stack[] =
        [
            __FUNCTION__,
            'POST /user/repos',
            'name' => $this->repo,
        ];
        $this->allowedOptionalParameters =
        [
            'description',
            'homepage',
            'private',
            'has_issues',
            'has_wiki',
            'has_downloads',
            'auto_init',
            'gitignore_template',
            'license_template',
        ];
        return $this;
    }

    /**
     * Create a new repository owned by the specified organization.
     */
    public function createRepository()
    {
        $this->stack[] =
        [
            __FUNCTION__,
            "POST /orgs/:owner/repos",
            'name' => $this->repo,
        ];
        $this->allowedOptionalParameters =
        [
            'description',
            'homepage',
            'private',
            'has_issues',
            'has_wiki',
            'has_downloads',
            'auto_init',
            'team_id',
            'gitignore_template',
            'license_template',
        ];
        return $this;
    }

    /**
     * Create a new repository owned by the specified organization.
     */
    public function updateRepository()
    {
        $this->stack[] =
        [
            __FUNCTION__,
            "PATCH /repos/:owner/:repo",
            'name' => $this->repo,
        ];
        $this->allowedOptionalParameters =
        [
            'description',
            'homepage',
            'private',
            'has_issues',
            'has_wiki',
            'has_downloads',
            'default_branch',
        ];
        return $this;
    }

    /**
     * Create a pull request in the specified repository.
     *
     * @param string $title PR title
     * @param string $head The name of the branch where your changes are
     *   implemented. For cross-repository pull requests in the same network,
     *   namespace head with a user like this: username:branch.
     * @param string $base The name of the branch you want your changes pulled
     *   into. This should be an existing branch on the current repository.
     *   You cannot submit a pull request to one repository that requests a
     *   merge to a base of another repository.
     * @return GitHubStack
     */
    public function createPR($title, $head, $base)
    {
        $this->stack[] =
        [
            __FUNCTION__,
            'POST /repos/:owner/:repo/pulls',
            'title' => $title,
            'head' => $head,
            'base' => $base,
        ];
        $this->allowedOptionalParameters =
        [
            'body',
        ];
        return $this;
    }

    /**
     * Create a label in the specified repository.
     *
     * @param string $labelName
     * @param string $color A 6 character hex code, without the leading #, identifying the color.
     * @return GitHubStack
     */
    public function createLabel($labelName, $color)
    {
        $this->stack[] =
        [
            __FUNCTION__,
            'POST /repos/:owner/:repo/labels',
            'name' => $labelName,
            'color' => $color,
        ];
        $this->allowedOptionalParameters = [];
        return $this;
    }

    /**
     * Change the assigned color of a given named label in the specified repository.
     *
     * @param string $labelName
     * @param string $color A 6 character hex code, without the leading #, identifying the color.
     * @return GitHubStack
     */
    public function updateLabel($labelName, $color)
    {
        $this->stack[] =
        [
            __FUNCTION__,
            "PATCH /repos/:owner/:repo/labels/$labelName",
            'name' => $labelName,
            'color' => $color,
        ];
        $this->allowedOptionalParameters = [];
        return $this;
    }

    /**
     * Delete a given named label in the specified repository.
     *
     * @param string $labelName
     * @return GitHubStack
     */
    public function deleteLabel($labelName)
    {
        $this->stack[] =
        [
            __FUNCTION__,
            "DELETE /repos/:owner/:repo/labels/$labelName",
        ];
        $this->allowedOptionalParameters = [];
        return $this;
    }

    /**
     * Add a label to the specified issue (or PR)
     *
     * @param string $issueNumber
     * @param string|array $labelName The name of a label, or an array
     *   containing multiple label names to add to the specified issue.
     * @return GitHubStack
     */
    public function addLabel($issueNumber, $labelName)
    {
        $this->stack[] = array_merge(
            [
                __FUNCTION__,
                "POST /repos/:owner/:repo/issues/$issueNumber/labels",
            ],
            (array)$labelName
        );
        $this->allowedOptionalParameters = [];
        return $this;
    }

    /**
     * Replace all labels on the specified issue (or PR) with the provided label(s)
     *
     * @param string $issueNumber
     * @param string|array $labelName The name of a label, or an array
     *   containing multiple label names to add to the specified issue.
     * @return GitHubStack
     */
    public function replaceLabels($issueNumber, $labelName)
    {
        $this->stack[] = array_merge(
            [
                __FUNCTION__,
                "PUT /repos/:owner/:repo/issues/$issueNumber/labels",
            ],
            (array)$labelName
        );
        $this->allowedOptionalParameters = [];
        return $this;
    }

    /**
     * Remove a given named label from a specified issue (or PR) in the specified repository.
     *
     * @param string $labelName
     * @return GitHubStack
     */
    public function removeLabel($issueNumber, $labelName)
    {
        $this->stack[] =
        [
            __FUNCTION__,
            "DELETE /repos/:owner/:repo/issues/$issueNumber/labels/$labelName",
        ];
        $this->allowedOptionalParameters = [];
        return $this;
    }

    /**
     * Remove all labels from a specified issue (or PR) in the specified repository.
     *
     * @param string $labelName
     * @return GitHubStack
     */
    public function removeAllLabels($issueNumber)
    {
        $this->stack[] =
        [
            __FUNCTION__,
            "DELETE /repos/:owner/:repo/issues/$issueNumber/labels",
        ];
        $this->allowedOptionalParameters = [];
        return $this;
    }

    /**
     * Set the body text for a pull request.
     *
     * @param type $body The body text.
     * @return GitHubStack
     */
    public function body($body)
    {
        return $this->setOptionalParameter('body', $body);
    }

    public function description($description)
    {
        return $this->setOptionalParameter('description', $description);
    }

    public function homepage($homepage)
    {
        return $this->setOptionalParameter('homepage', $homepage);
    }

    /**
     * Specify whether the new repository is private.  Use with createRepository and createUserRepository.
     *
     * @param bool $isPrivate Either true to create a private repository, or
     *   false to create a public one. Creating private repositories requires
     *   a paid GitHub account. Default: false
     * @return GitHubStack
     */
    public function isPrivate($isPrivate = true)
    {
        return $this->setOptionalBooleanParameter('private', $isPrivate);
    }

    /**
     * Specify whether the new repository has an issue queue.  Use with createRepository and createUserRepository.
     *
     * @param bool $hasIssues Either true to enable issues for this repository,
     *   false to disable them. Default: true
     * @return GitHubStack
     */
    public function hasIssues($hasIssues)
    {
        return $this->setOptionalBooleanParameter('has_issues', $hasIssues);
    }

    /**
     * Specify whether the new repository has a wiki.  Use with createRepository and createUserRepository.
     *
     * @param bool $hasWiki Either true to enable the wiki for this repository,
     *   false to disable it. Default: true
     * @return GitHubStack
     */
    public function hasWiki($hasWiki)
    {
        return $this->setOptionalBooleanParameter('has_wiki', $hasWiki);
    }

    /**
     * Specify whether the new repository has downloads. Use with createRepository and createUserRepository.
     *
     * @param type $hasDownloads Either true to enable downloads for this
     *   repository, false to disable them. Default: true
     * @return GitHubStack
     */
    public function hasDownloads($hasDownloads)
    {
        return $this->setOptionalBooleanParameter('has_downloads', $hasDownloads);
    }

    public function defaultBranch($defaultBranch)
    {
        return $this->setOptionalParameter('default_branch', $defaultBranch);
    }

    /**
     * Add a README to the repository. Use with createRepository and createUserRepository.
     *
     * @param type $autoInit Pass true to create an initial commit with empty
     *   README. Default: false
     * @return GitHubStack
     */
    public function autoInit($autoInit)
    {
        return $this->setOptionalBooleanParameter('auto_init', $autoInit);
    }

    /**
     * Specify which team in an organization that has access to the repository. Use with createRepository.
     *
     * @param type $teamId The id of the team that will be granted access to this repository.
     * @return GitHubStack
     */
    public function teamId($teamId)
    {
        return $this->setOptionalParameter('team_id', $teamId);
    }

    /**
     * Add a .gitignore file to the repository. Use with createRepository and createUserRepository.
     *
     * @param type $gitignoreTemplate Desired language or platform .gitignore
     *   template to apply. Use the name of the template without the extension.
     *   For example, "Haskell".
     * @return GitHubStack
     */
    public function gitignoreTemplate($gitignoreTemplate)
    {
        return $this->setOptionalParameter('gitignore_template', $gitignoreTemplate);
    }

    /**
     * Add a LICENSE to the repository. Use with createRepository and createUserRepository.
     *
     * @param type $licenseTemplate Desired LICENSE template to apply.
     *   Use the name of the template without the extension. For example, "mit" or "mozilla".
     * @return GitHubStack
     */
    public function licenseTemplate($licenseTemplate)
    {
        return $this->setOptionalParameter('license_template', $licenseTemplate);
    }

    protected function setOptionalBooleanParameter($paramName, $paramValue)
    {
        return $this->setOptionalParameter($paramName. $paramValue ? 'true' : 'false');
    }

    protected function setOptionalParameter($paramName, $paramValue)
    {
        if (empty($this->stack)) {
            throw new TaskException($this, "Must add at least one operation to the GitHubStack before setting the optional parameter '$paramName'");
        }
        $op = array_pop($this->stack);
        if (!in_array($paramName, $this->allowedOptionalParameters)) {
            $fn = array_shift($op);
            throw new TaskException($this, "The GitHub operation $fn does not support the optional parameter $paramName");
        }
        $op[$paramName] = $paramValue;
        $this->stack[] = $op;

        return $this;
    }

    /**
     * Print progress about the commands being executed
     */
    protected function printTaskProgress($command, $uri, $uri)
    {
        $this->printTaskInfo('{command} {uri} {params}', ['command' => "$command", 'uri' => $uri, 'params' => json_encode($uri)]);
    }

    /**
     * Allow owning collection to determine how many steps in this task.
     */
    public function progressIndicatorSteps()
    {
        return count($this->stack);
    }

    /**
     * Run all of the queued objects on the stack
     */
    public function run()
    {
        $this->startProgressIndicator();
        $result = Result::success($this);

        foreach ($this->stack as $params) {
            $fn = array_shift($params);
            $uri = array_shift($params);
            $this->printTaskProgress($fn, $uri, $params);
            $this->advanceProgressIndicator();
            // TODO: merge data from the result on this call
            // with data from the result on the previous call?
            // For now, the result always comes from the last function.
            $result = $this->sendRequest($uri, $params);
            if (!$result->wasSuccessful()) {
                break;
            }
        }

        $this->stopProgressIndicator();

        $result['time'] = $this->getExecutionTime();
        return $result;
    }
}
