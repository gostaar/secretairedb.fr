<?php
namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordModel
{
    
    /**
     * @Assert\NotBlank(message="Le mot de passe actuel est requis.")
     */
    private ?string $currentPassword = null;

    /**
     * @Assert\NotBlank(message="Le nouveau mot de passe est requis.")
     * @Assert\Length(
     *     min=8,
     *     max=64,
     *     minMessage="Le mot de passe doit contenir au moins {{ limit }} caractères.",
     *     maxMessage="Le mot de passe ne peut pas dépasser {{ limit }} caractères."
     * )
     */
    private ?string $newPassword = null;

    /**
     * @Assert\NotBlank(message="Veuillez confirmer le nouveau mot de passe.")
     * @Assert\EqualTo(
     *     propertyPath="newPassword",
     *     message="Les mots de passe ne correspondent pas."
     * )
     */
    private ?string $confirmNewPassword = null;

    // Getters et setters

    public function getCurrentPassword(): ?string
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword(?string $currentPassword): self
    {
        $this->currentPassword = $currentPassword;
        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): self
    {
        $this->newPassword = $newPassword;
        return $this;
    }

    public function getConfirmNewPassword(): ?string
    {
        return $this->confirmNewPassword;
    }

    public function setConfirmNewPassword(?string $confirmNewPassword): self
    {
        $this->confirmNewPassword = $confirmNewPassword;
        return $this;
    }
}
