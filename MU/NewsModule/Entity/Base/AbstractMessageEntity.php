<?php
/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\UsersModule\Entity\UserEntity;
use MU\NewsModule\Traits\StandardFieldsTrait;
use MU\NewsModule\Validator\Constraints as NewsAssert;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for message entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractMessageEntity extends EntityAccess implements Translatable
{
    /**
     * Hook standard fields behaviour embedding createdBy, updatedBy, createdDate, updatedDate fields.
     */
    use StandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'message';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\LessThan(value=1000000000)
     * @var integer $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     *
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @NewsAssert\ListEntry(entityName="message", propertyName="workflowState", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $title
     */
    protected $title = '';
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=10000)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="10000")
     * @var text $startText
     */
    protected $startText = '';
    
    /**
     * Image upload 1 meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $imageUpload1Meta
     */
    protected $imageUpload1Meta = [];
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @Assert\File(
     *    maxSize = "200k",
     *    mimeTypes = {"image/*"}
     * )
     * @Assert\Image(
     * )
     * @var string $imageUpload1
     */
    protected $imageUpload1 = null;
    
    /**
     * Full image upload 1 path as url.
     *
     * @Assert\Type(type="string")
     * @var string $imageUpload1Url
     */
    protected $imageUpload1Url = '';
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=20000)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="20000")
     * @var text $mainText
     */
    protected $mainText = '';
    
    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\LessThan(value=100000000000)
     * @var integer $amountOfViews
     */
    protected $amountOfViews = 0;
    
    /**
     * @ORM\Column(length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="100")
     * @var string $author
     */
    protected $author = '';
    
    /**
     * @ORM\ManyToOne(targetEntity="Zikula\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(referencedColumnName="uid", nullable=true)
     * @var UserEntity $approver
     */
    protected $approver = null;
    
    /**
     * @ORM\Column(type="text", length=2000)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="2000")
     * @var text $notes
     */
    protected $notes = '';
    
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $displayOnIndex
     */
    protected $displayOnIndex = true;
    
    /**
     * @ORM\Column(length=7)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="7")
     * @Assert\Locale()
     * @var string $messageLanguage
     */
    protected $messageLanguage = '';
    
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $allowComments
     */
    protected $allowComments = true;
    
    /**
     * Image upload 2 meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $imageUpload2Meta
     */
    protected $imageUpload2Meta = [];
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @Assert\File(
     *    maxSize = "200k",
     *    mimeTypes = {"image/*"}
     * )
     * @Assert\Image(
     * )
     * @var string $imageUpload2
     */
    protected $imageUpload2 = null;
    
    /**
     * Full image upload 2 path as url.
     *
     * @Assert\Type(type="string")
     * @var string $imageUpload2Url
     */
    protected $imageUpload2Url = '';
    
    /**
     * Image upload 3 meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $imageUpload3Meta
     */
    protected $imageUpload3Meta = [];
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @Assert\File(
     *    maxSize = "200k",
     *    mimeTypes = {"image/*"}
     * )
     * @Assert\Image(
     * )
     * @var string $imageUpload3
     */
    protected $imageUpload3 = null;
    
    /**
     * Full image upload 3 path as url.
     *
     * @Assert\Type(type="string")
     * @var string $imageUpload3Url
     */
    protected $imageUpload3Url = '';
    
    /**
     * Image upload 4 meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $imageUpload4Meta
     */
    protected $imageUpload4Meta = [];
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @Assert\File(
     *    maxSize = "200k",
     *    mimeTypes = {"image/*"}
     * )
     * @Assert\Image(
     * )
     * @var string $imageUpload4
     */
    protected $imageUpload4 = null;
    
    /**
     * Full image upload 4 path as url.
     *
     * @Assert\Type(type="string")
     * @var string $imageUpload4Url
     */
    protected $imageUpload4Url = '';
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     * @var DateTime $startDate
     */
    protected $startDate;
    
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $noEndDate
     */
    protected $noEndDate = true;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     * @Assert\Expression("!value or value > this.getStartDate()", message="The start must be before the end.")
     * @var DateTime $endDate
     */
    protected $endDate;
    
    /**
     * @ORM\Column(type="smallint")
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\LessThan(value=100)
     * @var integer $weight
     */
    protected $weight = 1;
    
    
    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"title"}, updatable=true, unique=true, separator="-", style="lower")
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(min="1", max="255")
     * @var string $slug
     */
    protected $slug;
    
    /**
     * Used locale to override Translation listener's locale.
     * this is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\Locale()
     * @Gedmo\Locale
     * @var string $locale
     */
    protected $locale;
    
    /**
     * @ORM\OneToMany(targetEntity="\MU\NewsModule\Entity\MessageAttributeEntity", 
     *                mappedBy="entity", cascade={"all"}, 
     *                orphanRemoval=true, indexBy="name")
     * @var \MU\NewsModule\Entity\MessageAttributeEntity
     */
    protected $attributes = null;
    
    /**
     * @ORM\OneToMany(targetEntity="\MU\NewsModule\Entity\MessageCategoryEntity", 
     *                mappedBy="entity", cascade={"all"}, 
     *                orphanRemoval=true)
     * @var \MU\NewsModule\Entity\MessageCategoryEntity
     */
    protected $categories = null;
    
    
    /**
     * MessageEntity constructor.
     *
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     */
    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }
    
    /**
     * Returns the _object type.
     *
     * @return string
     */
    public function get_objectType()
    {
        return $this->_objectType;
    }
    
    /**
     * Sets the _object type.
     *
     * @param string $_objectType
     *
     * @return void
     */
    public function set_objectType($_objectType)
    {
        if ($this->_objectType != $_objectType) {
            $this->_objectType = $_objectType;
        }
    }
    
    
    /**
     * Returns the id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the id.
     *
     * @param integer $id
     *
     * @return void
     */
    public function setId($id)
    {
        if (intval($this->id) !== intval($id)) {
            $this->id = intval($id);
        }
    }
    
    /**
     * Returns the workflow state.
     *
     * @return string
     */
    public function getWorkflowState()
    {
        return $this->workflowState;
    }
    
    /**
     * Sets the workflow state.
     *
     * @param string $workflowState
     *
     * @return void
     */
    public function setWorkflowState($workflowState)
    {
        if ($this->workflowState !== $workflowState) {
            $this->workflowState = isset($workflowState) ? $workflowState : '';
        }
    }
    
    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Sets the title.
     *
     * @param string $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        if ($this->title !== $title) {
            $this->title = isset($title) ? $title : '';
        }
    }
    
    /**
     * Returns the start text.
     *
     * @return text
     */
    public function getStartText()
    {
        return $this->startText;
    }
    
    /**
     * Sets the start text.
     *
     * @param text $startText
     *
     * @return void
     */
    public function setStartText($startText)
    {
        if ($this->startText !== $startText) {
            $this->startText = isset($startText) ? $startText : '';
        }
    }
    
    /**
     * Returns the image upload 1.
     *
     * @return string
     */
    public function getImageUpload1()
    {
        return $this->imageUpload1;
    }
    
    /**
     * Sets the image upload 1.
     *
     * @param string $imageUpload1
     *
     * @return void
     */
    public function setImageUpload1($imageUpload1)
    {
        if ($this->imageUpload1 !== $imageUpload1) {
            $this->imageUpload1 = $imageUpload1;
        }
    }
    
    /**
     * Returns the image upload 1 url.
     *
     * @return string
     */
    public function getImageUpload1Url()
    {
        return $this->imageUpload1Url;
    }
    
    /**
     * Sets the image upload 1 url.
     *
     * @param string $imageUpload1Url
     *
     * @return void
     */
    public function setImageUpload1Url($imageUpload1Url)
    {
        if ($this->imageUpload1Url !== $imageUpload1Url) {
            $this->imageUpload1Url = $imageUpload1Url;
        }
    }
    
    /**
     * Returns the image upload 1 meta.
     *
     * @return array
     */
    public function getImageUpload1Meta()
    {
        return $this->imageUpload1Meta;
    }
    
    /**
     * Sets the image upload 1 meta.
     *
     * @param array $imageUpload1Meta
     *
     * @return void
     */
    public function setImageUpload1Meta($imageUpload1Meta = [])
    {
        if ($this->imageUpload1Meta !== $imageUpload1Meta) {
            $this->imageUpload1Meta = $imageUpload1Meta;
        }
    }
    
    /**
     * Returns the main text.
     *
     * @return text
     */
    public function getMainText()
    {
        return $this->mainText;
    }
    
    /**
     * Sets the main text.
     *
     * @param text $mainText
     *
     * @return void
     */
    public function setMainText($mainText)
    {
        if ($this->mainText !== $mainText) {
            $this->mainText = isset($mainText) ? $mainText : '';
        }
    }
    
    /**
     * Returns the amount of views.
     *
     * @return integer
     */
    public function getAmountOfViews()
    {
        return $this->amountOfViews;
    }
    
    /**
     * Sets the amount of views.
     *
     * @param integer $amountOfViews
     *
     * @return void
     */
    public function setAmountOfViews($amountOfViews)
    {
        if (intval($this->amountOfViews) !== intval($amountOfViews)) {
            $this->amountOfViews = intval($amountOfViews);
        }
    }
    
    /**
     * Returns the author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }
    
    /**
     * Sets the author.
     *
     * @param string $author
     *
     * @return void
     */
    public function setAuthor($author)
    {
        if ($this->author !== $author) {
            $this->author = isset($author) ? $author : '';
        }
    }
    
    /**
     * Returns the approver.
     *
     * @return UserEntity
     */
    public function getApprover()
    {
        return $this->approver;
    }
    
    /**
     * Sets the approver.
     *
     * @param UserEntity $approver
     *
     * @return void
     */
    public function setApprover($approver)
    {
        if ($this->approver !== $approver) {
            $this->approver = $approver;
        }
    }
    
    /**
     * Returns the notes.
     *
     * @return text
     */
    public function getNotes()
    {
        return $this->notes;
    }
    
    /**
     * Sets the notes.
     *
     * @param text $notes
     *
     * @return void
     */
    public function setNotes($notes)
    {
        if ($this->notes !== $notes) {
            $this->notes = isset($notes) ? $notes : '';
        }
    }
    
    /**
     * Returns the display on index.
     *
     * @return boolean
     */
    public function getDisplayOnIndex()
    {
        return $this->displayOnIndex;
    }
    
    /**
     * Sets the display on index.
     *
     * @param boolean $displayOnIndex
     *
     * @return void
     */
    public function setDisplayOnIndex($displayOnIndex)
    {
        if (boolval($this->displayOnIndex) !== boolval($displayOnIndex)) {
            $this->displayOnIndex = boolval($displayOnIndex);
        }
    }
    
    /**
     * Returns the message language.
     *
     * @return string
     */
    public function getMessageLanguage()
    {
        return $this->messageLanguage;
    }
    
    /**
     * Sets the message language.
     *
     * @param string $messageLanguage
     *
     * @return void
     */
    public function setMessageLanguage($messageLanguage)
    {
        if ($this->messageLanguage !== $messageLanguage) {
            $this->messageLanguage = isset($messageLanguage) ? $messageLanguage : '';
        }
    }
    
    /**
     * Returns the allow comments.
     *
     * @return boolean
     */
    public function getAllowComments()
    {
        return $this->allowComments;
    }
    
    /**
     * Sets the allow comments.
     *
     * @param boolean $allowComments
     *
     * @return void
     */
    public function setAllowComments($allowComments)
    {
        if (boolval($this->allowComments) !== boolval($allowComments)) {
            $this->allowComments = boolval($allowComments);
        }
    }
    
    /**
     * Returns the image upload 2.
     *
     * @return string
     */
    public function getImageUpload2()
    {
        return $this->imageUpload2;
    }
    
    /**
     * Sets the image upload 2.
     *
     * @param string $imageUpload2
     *
     * @return void
     */
    public function setImageUpload2($imageUpload2)
    {
        if ($this->imageUpload2 !== $imageUpload2) {
            $this->imageUpload2 = $imageUpload2;
        }
    }
    
    /**
     * Returns the image upload 2 url.
     *
     * @return string
     */
    public function getImageUpload2Url()
    {
        return $this->imageUpload2Url;
    }
    
    /**
     * Sets the image upload 2 url.
     *
     * @param string $imageUpload2Url
     *
     * @return void
     */
    public function setImageUpload2Url($imageUpload2Url)
    {
        if ($this->imageUpload2Url !== $imageUpload2Url) {
            $this->imageUpload2Url = $imageUpload2Url;
        }
    }
    
    /**
     * Returns the image upload 2 meta.
     *
     * @return array
     */
    public function getImageUpload2Meta()
    {
        return $this->imageUpload2Meta;
    }
    
    /**
     * Sets the image upload 2 meta.
     *
     * @param array $imageUpload2Meta
     *
     * @return void
     */
    public function setImageUpload2Meta($imageUpload2Meta = [])
    {
        if ($this->imageUpload2Meta !== $imageUpload2Meta) {
            $this->imageUpload2Meta = $imageUpload2Meta;
        }
    }
    
    /**
     * Returns the image upload 3.
     *
     * @return string
     */
    public function getImageUpload3()
    {
        return $this->imageUpload3;
    }
    
    /**
     * Sets the image upload 3.
     *
     * @param string $imageUpload3
     *
     * @return void
     */
    public function setImageUpload3($imageUpload3)
    {
        if ($this->imageUpload3 !== $imageUpload3) {
            $this->imageUpload3 = $imageUpload3;
        }
    }
    
    /**
     * Returns the image upload 3 url.
     *
     * @return string
     */
    public function getImageUpload3Url()
    {
        return $this->imageUpload3Url;
    }
    
    /**
     * Sets the image upload 3 url.
     *
     * @param string $imageUpload3Url
     *
     * @return void
     */
    public function setImageUpload3Url($imageUpload3Url)
    {
        if ($this->imageUpload3Url !== $imageUpload3Url) {
            $this->imageUpload3Url = $imageUpload3Url;
        }
    }
    
    /**
     * Returns the image upload 3 meta.
     *
     * @return array
     */
    public function getImageUpload3Meta()
    {
        return $this->imageUpload3Meta;
    }
    
    /**
     * Sets the image upload 3 meta.
     *
     * @param array $imageUpload3Meta
     *
     * @return void
     */
    public function setImageUpload3Meta($imageUpload3Meta = [])
    {
        if ($this->imageUpload3Meta !== $imageUpload3Meta) {
            $this->imageUpload3Meta = $imageUpload3Meta;
        }
    }
    
    /**
     * Returns the image upload 4.
     *
     * @return string
     */
    public function getImageUpload4()
    {
        return $this->imageUpload4;
    }
    
    /**
     * Sets the image upload 4.
     *
     * @param string $imageUpload4
     *
     * @return void
     */
    public function setImageUpload4($imageUpload4)
    {
        if ($this->imageUpload4 !== $imageUpload4) {
            $this->imageUpload4 = $imageUpload4;
        }
    }
    
    /**
     * Returns the image upload 4 url.
     *
     * @return string
     */
    public function getImageUpload4Url()
    {
        return $this->imageUpload4Url;
    }
    
    /**
     * Sets the image upload 4 url.
     *
     * @param string $imageUpload4Url
     *
     * @return void
     */
    public function setImageUpload4Url($imageUpload4Url)
    {
        if ($this->imageUpload4Url !== $imageUpload4Url) {
            $this->imageUpload4Url = $imageUpload4Url;
        }
    }
    
    /**
     * Returns the image upload 4 meta.
     *
     * @return array
     */
    public function getImageUpload4Meta()
    {
        return $this->imageUpload4Meta;
    }
    
    /**
     * Sets the image upload 4 meta.
     *
     * @param array $imageUpload4Meta
     *
     * @return void
     */
    public function setImageUpload4Meta($imageUpload4Meta = [])
    {
        if ($this->imageUpload4Meta !== $imageUpload4Meta) {
            $this->imageUpload4Meta = $imageUpload4Meta;
        }
    }
    
    /**
     * Returns the start date.
     *
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
    
    /**
     * Sets the start date.
     *
     * @param DateTime $startDate
     *
     * @return void
     */
    public function setStartDate($startDate)
    {
        if ($this->startDate !== $startDate) {
            if (!(null == $startDate && empty($startDate)) && !(is_object($startDate) && $startDate instanceOf \DateTimeInterface)) {
                $startDate = new \DateTime($startDate);
            }
            
            if ($this->startDate != $startDate) {
                $this->startDate = $startDate;
            }
        }
    }
    
    /**
     * Returns the no end date.
     *
     * @return boolean
     */
    public function getNoEndDate()
    {
        return $this->noEndDate;
    }
    
    /**
     * Sets the no end date.
     *
     * @param boolean $noEndDate
     *
     * @return void
     */
    public function setNoEndDate($noEndDate)
    {
        if (boolval($this->noEndDate) !== boolval($noEndDate)) {
            $this->noEndDate = boolval($noEndDate);
        }
    }
    
    /**
     * Returns the end date.
     *
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
    
    /**
     * Sets the end date.
     *
     * @param DateTime $endDate
     *
     * @return void
     */
    public function setEndDate($endDate)
    {
        if ($this->endDate !== $endDate) {
            if (!(null == $endDate && empty($endDate)) && !(is_object($endDate) && $endDate instanceOf \DateTimeInterface)) {
                $endDate = new \DateTime($endDate);
            }
            
            if ($this->endDate != $endDate) {
                $this->endDate = $endDate;
            }
        }
    }
    
    /**
     * Returns the weight.
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }
    
    /**
     * Sets the weight.
     *
     * @param integer $weight
     *
     * @return void
     */
    public function setWeight($weight)
    {
        if (intval($this->weight) !== intval($weight)) {
            $this->weight = intval($weight);
        }
    }
    
    /**
     * Returns the slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
    /**
     * Sets the slug.
     *
     * @param string $slug
     *
     * @return void
     */
    public function setSlug($slug)
    {
        if ($this->slug != $slug) {
            $this->slug = $slug;
        }
    }
    
    /**
     * Returns the locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    /**
     * Sets the locale.
     *
     * @param string $locale
     *
     * @return void
     */
    public function setLocale($locale)
    {
        if ($this->locale != $locale) {
            $this->locale = $locale;
        }
    }
    
    /**
     * Returns the attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Set attribute.
     *
     * @param string $name  Attribute name
     * @param string $value Attribute value
     *
     * @return void
     */
    public function setAttribute($name, $value)
    {
        if (isset($this->attributes[$name])) {
            if (null === $value) {
                $this->attributes->remove($name);
            } else {
                $this->attributes[$name]->setValue($value);
            }
        } else {
            $this->attributes[$name] = new \MU\NewsModule\Entity\MessageAttributeEntity($name, $value, $this);
        }
    }
    
    /**
     * Returns the categories.
     *
     * @return ArrayCollection[]
     */
    public function getCategories()
    {
        return $this->categories;
    }
    
    
    /**
     * Sets the categories.
     *
     * @param ArrayCollection $categories List of categories
     *
     * @return void
     */
    public function setCategories(ArrayCollection $categories)
    {
        foreach ($this->categories as $category) {
            if (false === $key = $this->collectionContains($categories, $category)) {
                $this->categories->removeElement($category);
            } else {
                $categories->remove($key);
            }
        }
        foreach ($categories as $category) {
            $this->categories->add($category);
        }
    }
    
    /**
     * Checks if a collection contains an element based only on two criteria (categoryRegistryId, category).
     *
     * @param ArrayCollection $collection Given collection
     * @param \MU\NewsModule\Entity\MessageCategoryEntity $element Element to search for
     *
     * @return bool|int
     */
    private function collectionContains(ArrayCollection $collection, \MU\NewsModule\Entity\MessageCategoryEntity $element)
    {
        foreach ($collection as $key => $category) {
            /** @var \MU\NewsModule\Entity\MessageCategoryEntity $category */
            if ($category->getCategoryRegistryId() == $element->getCategoryRegistryId()
                && $category->getCategory() == $element->getCategory()
            ) {
                return $key;
            }
        }
    
        return false;
    }
    
    
    
    /**
     * Checks whether the approver field contains a valid user reference.
     * This method is used for validation.
     *
     * @Assert\IsTrue(message="This value must be a valid user id.")
     *
     * @return boolean True if data is valid else false
     */
    public function isApproverUserValid()
    {
        return null === $this['approver'] || $this['approver'] instanceof UserEntity;
    }
    
    /**
     * Creates url arguments array for easy creation of display urls.
     *
     * @return array List of resulting arguments
     */
    public function createUrlArgs()
    {
        return [
            'slug' => $this->getSlug()
        ];
    }
    
    /**
     * Returns the primary key.
     *
     * @return integer The identifier
     */
    public function getKey()
    {
        return $this->getId();
    }
    
    /**
     * Determines whether this entity supports hook subscribers or not.
     *
     * @return boolean
     */
    public function supportsHookSubscribers()
    {
        return true;
    }
    
    /**
     * Return lower case name of multiple items needed for hook areas.
     *
     * @return string
     */
    public function getHookAreaPrefix()
    {
        return 'munewsmodule.ui_hooks.messages';
    }
    
    /**
     * Returns an array of all related objects that need to be persisted after clone.
     * 
     * @param array $objects Objects that are added to this array
     * 
     * @return array List of entity objects
     */
    public function getRelatedObjectsToPersist(&$objects = [])
    {
        return [];
    }
    
    /**
     * ToString interceptor implementation.
     * This method is useful for debugging purposes.
     *
     * @return string The output string for this entity
     */
    public function __toString()
    {
        return 'Message ' . $this->getKey() . ': ' . $this->getTitle();
    }
    
    /**
     * Clone interceptor implementation.
     * This method is for example called by the reuse functionality.
     * Performs a quite simple shallow copy.
     *
     * See also:
     * (1) http://docs.doctrine-project.org/en/latest/cookbook/implementing-wakeup-or-clone.html
     * (2) http://www.php.net/manual/en/language.oop5.cloning.php
     * (3) http://stackoverflow.com/questions/185934/how-do-i-create-a-copy-of-an-object-in-php
     */
    public function __clone()
    {
        // if the entity has no identity do nothing, do NOT throw an exception
        if (!$this->id) {
            return;
        }
    
        // otherwise proceed
    
        // unset identifier
        $this->setId(0);
    
        // reset workflow
        $this->setWorkflowState('initial');
    
        // reset upload fields
        $this->setImageUpload1(null);
        $this->setImageUpload1Meta([]);
        $this->setImageUpload1Url('');
        $this->setImageUpload2(null);
        $this->setImageUpload2Meta([]);
        $this->setImageUpload2Url('');
        $this->setImageUpload3(null);
        $this->setImageUpload3Meta([]);
        $this->setImageUpload3Url('');
        $this->setImageUpload4(null);
        $this->setImageUpload4Meta([]);
        $this->setImageUpload4Url('');
    
        $this->setCreatedBy(null);
        $this->setCreatedDate(null);
        $this->setUpdatedBy(null);
        $this->setUpdatedDate(null);
    
    
        // clone categories
        $categories = $this->categories;
        $this->categories = new ArrayCollection();
        foreach ($categories as $c) {
            $newCat = clone $c;
            $this->categories->add($newCat);
            $newCat->setEntity($this);
        }
    
        // clone attributes
        $attributes = $this->attributes;
        $this->attributes = new ArrayCollection();
        foreach ($attributes as $a) {
            $newAttr = clone $a;
            $this->attributes->add($newAttr);
            $newAttr->setEntity($this);
        }
    }
}
