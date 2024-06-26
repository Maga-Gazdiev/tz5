<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Http\Resources\UserResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [  
            "id"=> $this->id,
            "user" => new UserResource(User::findOrFail($this->user_id)),
            "title" => $this->title,
            "content" => $this->content,
            "created_at" => $this->created_at,
        ];
    }
    
}
