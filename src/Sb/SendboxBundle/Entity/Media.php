<?php
    
namespace Sb\SendboxBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Sb\SendboxBundle\Entity\Announce
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sb\SendboxBundle\Repository\MediaRepository")
 */
class Media
{
  
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Announce", inversedBy="medias", cascade={"remove"})
     * @ORM\JoinColumn(name="announce_id", referencedColumnName="id")
     */
    private $announce;

    /**
     * @ORM\Column(name="title", length=255, nullable=true, type="string")
     */
    private $title;

    /**
     * @ORM\Column(name="content", nullable=true, type="string")
     */
    private $content;

    /**
     * @ORM\Column(name="filesize", nullable=true, type="integer")
     */
    private $filesize;

    /**
     * @ORM\Column(name="file_type", length=255, nullable=true, type="string")
     */
    private $mime_type;

    /**
     * @ORM\Column(name="ordering", nullable=true, type="integer")
     */
    private $ordering = 0;

    /**
     * @ORM\Column(name="cnt_download", nullable=true, type="integer")
     */
    private $cnt_download = 0;
    
    /**
     * @ORM\Column(name="is_published", type="boolean")
     */
    private $is_published = 0;
    
    /**
     * @ORM\Column(name="created_at",type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;
    
     /**
     * @ORM\Column(name="updated_at",type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated_at;

    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set filesize
     *
     * @param string $filesize
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;
    }

    /**
     * Get filesize
     *
     * @return string 
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * Set mime_type
     *
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mime_type = $mimeType;
    }

    /**
     * Get mime_type
     *
     * @return string 
     */
    public function getMimeType()
    {
        return $this->mime_type;
    }

    /**
     * Set ordering
     *
     * @param string $ordering
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
    }

    /**
     * Get ordering
     *
     * @return string 
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Set cnt_download
     *
     * @param string $cntDownload
     */
    public function setCntDownload($cntDownload)
    {
        $this->cnt_download = $cntDownload;
    }

    /**
     * Get cnt_download
     *
     * @return string 
     */
    public function getCntDownload()
    {
        return $this->cnt_download;
    }

    /**
     * Set is_published
     *
     * @param string $isPublished
     */
    public function setIsPublished($isPublished)
    {
        $this->is_published = $isPublished;
    }

    /**
     * Get is_published
     *
     * @return string 
     */
    public function getIsPublished()
    {
        return $this->is_published;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set user
     *
     * @param Sp\SpotoraBundle\Entity\User $user
     */
    public function setUser(\Sp\SpotoraBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Sp\SpotoraBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set announce
     *
     * @param Sp\SpotoraBundle\Entity\Announce $announce
     */
    public function setAnnounce(\Sp\SpotoraBundle\Entity\Announce $announce)
    {
        $this->announce = $announce;
    }

    /**
     * Get announce
     *
     * @return Sp\SpotoraBundle\Entity\Announce 
     */
    public function getAnnounce()
    {
        return $this->announce;
    }
}
