<?php
namespace WellGedaan\UrenRegistratie\Model;


/**
 * @Entity
 * @Table(name="hourregistrations")
 **/
class HourRegistration
{
    /**
     * @Id
     * @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user", referencedColumnName="id")
     *
     * @var User
     */
    protected $user;

    /**
     * @Column(type="datetime")
     */
    protected $day;

    /**
     * @Column(type="datetime")
     */
    protected $startingTime;

    /**
     * @Column(type="datetime")
     */
    protected $endTime;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getStartingTime()
    {
        return $this->startingTime;
    }

    /**
     * @param mixed $startingTime
     */
    public function setStartingTime($startingTime)
    {
        $this->startingTime = $startingTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

}