<?php

namespace Database\Seeders;

use Date;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Group;
use App\Models\Friends;
use App\Models\RoleUser;
use App\Models\Actualites;
use App\Models\Invitation;
use App\Models\MembreGroup;
use App\Models\Publication;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        RoleUser::create([
            'type' => 'admin'
        ]);
        RoleUser::create([
            'type' => 'membre'
        ]);
        \App\Models\User::factory(150)->create();

        $allUsers = User::all();
        foreach ($allUsers as $user) {
            $numberPub = rand(5,10);
            for ($i=0; $i < $numberPub; $i++) { 
                $faker = Faker::create();
                $publication = $user->publicationStatus()->create([
                    'user_id' => $user->id,
                    'description' => $faker->paragraph(rand(1,3))
                ]);
                $rand = rand(1,2);
                if ($rand == 1) {
                    $publication->media()->create([
                        'file' => 'https://picsum.photos/250/176',
                        'type' => 'image',
                        'active' => true
                    ]);
                }
                
                $publication->actualites()->create();
            }
        }

        $randomUser = User::inRandomOrder()->take(50)->get();
        foreach ($randomUser as $user) {
            $newGroup = $user->groups()->create([
                'name' =>  'groupe-'.$user->id
            ]);
            
            $newGroup->membresGroupe()->create([
                'user_id' => $newGroup->user_id,
                'role_id' => RoleUser::where('type','admin')->first()->id
            ]);
        }

        $allGroupe = Group::all();
        foreach ($allGroupe as $groupe) {
            $randomUser = User::inRandomOrder()->take(rand(50,100))->get();
            foreach ($randomUser as $user) {
                if ($groupe->user_id != $user->id) {
                    $exist = MembreGroup::where(['user_id' => $user->id , 'group_id' => $groupe->id ])->first();
                    if (!isset($exist)) {
                        $invitations = $groupe->invitable()->create([
                            'invite' => $user->id
                        ]);
                    }
                }
            }
        }

        //accepte invitation groupe
        $invitations = Invitation::where('invitable_type','App\Models\Group')->inRandomOrder()->take(200)->get();
        foreach ($invitations as $invitation) {
            $invitation->update([
                'status' => 1
            ]);
            $groupe = Group::find($invitation->invitable_id);
            $groupe->membresGroupe()->create([
                'user_id' => $invitation->invite,
                'role_id' => RoleUser::where('type','membre')->first()->id
            ]);
        }


        //create publication dans un groupe
        $membres = MembreGroup::inRandomOrder()->get();
        foreach ($membres as $membre) {
            $faker = Faker::create();
            $group = Group::find($membre->group_id);
            $publication = $group->publicable()->create([
                'user_id' => $membre->user_id,
                'description' => $faker->paragraph(rand(2,5))
            ]);
            $rand = rand(1,2);
            if ($rand == 1) {
                $publication->media()->create([
                    'file' => 'https://picsum.photos/250/176',
                    'type' => 'image',
                    'active' => true
                ]);
            }
            $publication->actualites()->create();
        }

        //creer commentaire
        $groups = Group::all();
        foreach ($groups as $group) {
            $countMembre = $group->membresGroupe->count();
            if ($countMembre > 0) {
                $publications = Publication::where('publicable_type','App\Models\Group')->where('publicable_id',$group->id)->get();
                foreach ($publications as $publication) {
                    $membres = MembreGroup::inRandomOrder()->take(rand(30,$countMembre))->get();
                    foreach ($membres as $membre) {
                        $faker = Faker::create();
                        $commentaire = $publication->commentaires()->create([
                            'user_id' => $membre->user_id,
                            'message' => $faker->paragraph(rand(2,5))
                        ]);
                    }
                }
            }
        }

        // send invitaion amis
        $allUser = User::all();
        foreach ($allUser as $user) {
            $others = User::where('id','<>',$user->id)->inRandomOrder()->take(rand(25,75))->get();
            foreach ($others as $other) {
                $exist1 = Invitation::where('invite',$other->id)->where('inviteur',$user->id)->where('invitable_type','App\Models\User')->first();
                $exist2 = Invitation::where('invite',$user->id)->where('inviteur',$other->id)->where('invitable_type','App\Models\User')->first();
                if ((!isset($exist1)) && (!isset($exist2))) {
                    $invit = $user->invitations()->create([
                        'inviteur' => $user->id,
                        'status' => 0,
                        'invite' => $other->id,
                    ]);
                }
            }
        }

        //accepte invitaion amis
            $invitations = Invitation::where('invitable_type','App\Models\User')->inRandomOrder()->take(100)->get();
            foreach ($invitations as $invitation) {
                $invitation->update(['status' => 1]);
                DB::table('friends_pivot')->insert([
                    ['user_id' => $invitation->inviteur,'friend_id' => $invitation->invite],
                    ['user_id' => $invitation->invite,'friend_id' => $invitation->inviteur]
                ]);
                // Friends::create([
                //     'friend_id'=>$invitation->invite,
                //     'user_id' => $invitation->inviteur
                // ]);
                // Friends::create([
                //     'friend_id'=>$invitation->inviteur,
                //     'user_id'=>$invitation->invite
                // ]);
            }

        $actualites = Actualites::all();
        foreach ($actualites as $actualite) {
            $date = Carbon::now()->subDays(rand(1,365));
            $publication = $actualite->actualable ;
            $publication->update([
                'created_at' => $date,
                'updated_at' => $date
            ]);
            $actualite->update([
                'created_at' => $date,
                'updated_at' => $date
            ]);
        }

        // //media
        // $publications = Publication::all();
        // foreach ($publications as $publication) {
        //     $rand = rand(1,0);
        //     if ($rand == 1) {
        //         $publication->media()->create([
        //             'file' => 'https://picsum.photos/250/176',
        //             'type' => 'image',
        //             'active' => true
        //         ]);
        //     }
        // }

        $users = User::all();
        foreach ($users as $user) {
            $friends = $user->friends;
            foreach ($friends as $friend) {
                $publications = $friend->publicationStatus ;
                foreach ($publications as $publication) {
                    $rand = rand(1,3);
                    $faker = Faker::create();
                    if ($rand == 3) {
                        $share = $publication->sharable()->create([
                            'user_id' => $user->id,
                        ]);

                        

                        $publicable = $share->publicable()->create([
                            'description' => $faker->paragraph(rand(2,5)),
                            'user_id' =>$user->id
                        ]);
                        $rand = rand(1,2);
                        if ($rand == 1) {
                            $publicable->media()->create([
                                'file' => 'https://picsum.photos/250/176',
                                'type' => 'image',
                                'active' => true
                            ]);
                        }
                
                        $publicable->actualites()->create();
                    }
                }
            }
        }
    }
}
