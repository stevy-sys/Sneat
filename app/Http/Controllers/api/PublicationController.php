<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Group;
use App\Models\Publication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Reaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\Request as ClientRequest;

class PublicationController extends Controller
{
    protected $auth ;
    public function __construct() {

        $this->auth = User::find(Auth::id());
    }


    /**
     * @OA\Post(
     *      path="/api/publiez/publiez",
     *      operationId="publiezInGroup",
     *      tags={"Publications"},
     *      summary="Publier un statut dans un groupe",
     *      description="Publiez dans le groupe pour que ca se voit aussi sur actu des amis ou non amis membre",
     *      @OA\RequestBody(
     *          description="Données du statut à publier",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="id_group",
     *                  type="integer",
     *                  example="bonjour tout le monde"
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  example="bonjour tout le monde"
     *              ),
     *              @OA\Property(
     *                  property="file",
     *                  type="string",
     *                  example="base 64"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Statut publié avec succès",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function publiezInGroup(Request $request)
    {
        $group = Group::find($request->id_group);
        $publication = $group->publicable()->create([
            'user_id' => Auth::id(),
            'description' => $request->description
        ]);
        
        if ($request->file) {
            $media = $this->decodebase64($request->file);
            $publication->media()->create([
                'file' => $media['path'], 
                'type' => $media['type'],
            ]);
        }
        $publication->actualites()->create();
        return response()->json([
            'data' => $publication
        ],201);
    }

    /**
     * @OA\Post(
     *      path="/api/publier/create",
     *      operationId="publierStatut",
     *      tags={"Publications"},
     *      summary="Publier un statut",
     *      description="Publier un statut en envoyant les données du statut dans le corps de la requête.",
     *      @OA\RequestBody(
     *          description="Données du statut à publier",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  example="bonjour tout le monde"
     *              ),
     *              @OA\Property(
     *                  property="file",
     *                  type="string",
     *                  example="dqsfjkqsdfkljqhsfdkjhqsfkj"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Statut publié avec succès",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function publierStatut (Request $request)
    {
        $publication = $this->auth->publicationStatus()->create([
            'user_id' => Auth::id(),
            'description' => $request->description
        ]);
        
        if ($request->file) {
            $media = $this->decodebase64($request->file);
            $publication->media()->create([
                'file' => $media['path'],
                'type' => $media['type'],
            ]);
        }
        
        $publication->actualites()->create();
        return response()->json([
            'data' => $publication
        ],201);
    }
    /**
     * @OA\Post(
     *      path="/api/publier/delete/{id_publication}",
     *      operationId="supprimerStatut",
     *      tags={"Publications"},
     *      summary="Suprimer une publication",
     *      description="Suprimer une publication en envoyant le id_publication",
     *      @OA\Parameter(
     *          name="id_publication",
     *          in="path",
     *          required=true,
     *          description="ID du publication",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Supression de publiction",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     * )
     */
    public function supprimerStatut($id_publication)
    {
        Publication::find($id_publication)->delete();
        return response()->json([
            'data' => 'publication delete'
        ],201);
    }

    public function modifier($id_publication,Request $request)
    {
        $publication = Publication::find($id_publication) ;
        $publication->update([
            'description' => $request->description
        ]);
        return response()->json([
            'data' => 'publication delete'
        ],201);
    }

    public function viewStatut($id_publication)
    {
        $publication = Publication::with(['user','commentaires.user'])->where('id',$id_publication)->first();
        return response()->json([
            'data' => $publication
        ],201);
    }

    public function reactPublication(Request $request)
    {
        $publication = Publication::find($request->id_publication);
        // dd($request->id_publication);
        $reactExist = $publication->reactable()->where('user_id',Auth::id())->first();
        if ($reactExist) {
            if ($reactExist->reaction_id == $request->id_reaction) {
                $publication->reactable()->where('user_id',Auth::id())->delete();
            }else{
                $publication->reactable()->where('user_id',Auth::id())->update([
                    'reaction_id' => $request->id_reaction
                ]);
            }
        }else{
            $reactExist = $publication->reactable()->create([
                'user_id' => Auth::id(),
                'reaction_id' => $request->id_reaction,
            ]);
            return response()->json([
                'data' => $reactExist
            ],201);
        }
        return response()->json([
            'data' => $reactExist
        ],201);
    }

    public function getAllReaction()
    {
        return Reaction::all();
    }
    
    private function decodebase64($data)
    {
        $image_64 = $data; //your base64 encoded data
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
        // find substring fro replace here eg: data:image/png;base64,
        $image = str_replace($replace, '', $image_64);
        $image = str_replace(' ', '+', $image);
        $imageName = uniqid() . '.' . $extension;
        $type = $this->detectType($extension);
        Storage::disk('publication')->put($imageName, base64_decode($image));
        return [
            'path' => $imageName ,
            'type' => $type
        ];
    }
    

    private function detectType($file)
    {
        $image = ['jpg', 'jpeg', 'png'];
        $video = ['mp4', 'avi'];
        $is_image = in_array($file, $image);
        $is_video = in_array($file, $video);

        if (!$is_video && !$is_image) {
            return 'file';
        }

        if ($is_image) {
            return 'image';
        }
        if ($is_video) {
            return 'video';
        }
    }
}
