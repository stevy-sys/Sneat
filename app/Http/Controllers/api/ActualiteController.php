<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Actualites;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActualiteController extends Controller
{
    public function getActualite()
    {
        $amis_id = getTableauAmis();
        $actualite = Actualites::whereHas(
            'actualable',
            function ($query) use ($amis_id) {
                $query->WhereHasMorph('publicable', ['App\Models\User'], function ($q) use ($amis_id) {
                    $q->whereIn('id', $amis_id);
                })->orWhereHasMorph('publicable', ['App\Models\Group'], function ($query) {
                    $query->whereHas('membresGroupe', function ($query) {
                        $query->where('user_id', Auth::id());
                    });
                })->orWhereHasMorph('publicable', ['App\Models\Shares'], function ($query) use ($amis_id) {
                    $query->whereIn('user_id', $amis_id);
                });
            },
        )
            ->with([
                'actualable.user',
                'actualable.publicable.sharable.user',
                'actualable.publicable.sharable.media',
                'actualable.publicable.sharable.commentaires.user',
                'actualable.media',
                'actualable.commentaires.user'
            ])->get()->pluck('actualable');
        return response()->json([
            'data' => $actualite
        ], 201);
    }
}
