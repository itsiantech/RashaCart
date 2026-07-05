@extends('admin.layouts.app')

@section('page-title')
    User | Edit
@endsection

@section('content')
  <div class="row">
    <div class="col-md-12">      
      <div class="card mb-4">
        <h5 class="card-header">Profile Details</h5>
        <!-- Account -->
        <div class="card-body">
          <div class="d-flex align-items-start align-items-sm-center gap-4">
            <img
              src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/admin/img/avatars/user-avater.png') }}"
              alt="user-avatar"
              class="d-block rounded"
              height="100"
              width="100"
              id="uploadedAvatar"
            />
            <div class="button-wrapper">
              <h2>{{Auth::user()->name}}</h2>
              <p class="text-muted mb-0">
                Role: {{ Auth::user()->getRoleNames()->join(', ') }}
              </p>
            
              <p class="text-muted mb-0">{{Auth::user()->address}}</p>
            </div>
          </div>
        </div>

        <hr class="my-0" />
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-6">            
              @include('profile.partials.update-profile-information-form')
            </div>
            <div class="col-12 col-md-6">   
              @include('profile.partials.update-password-form')
            </div>
          </div>
        </div>
        <!-- /Account -->
      </div>

      @if (!Auth::user()->hasRole('admin'))  
        <div class="card">
          <h5 class="card-header">Delete Account</h5>
          <div class="card-body">
          
              @include('profile.partials.delete-user-form')
          
          </div>
        </div>
      @endif

    </div>
  </div>
@endsection
