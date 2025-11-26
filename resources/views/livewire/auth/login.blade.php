 <!-- Register -->
          <div class="card col-xl-4  col-sm-12 offset-md-4">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a wire:navigate href="{{route('home')}}" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <img src="{{ asset('storage/logo/' . setting('app_logo')) }}" class="img-fluid" style="max-width: 150px; max-height: 50px;">
                  </span> 
                  {{-- <h3 class="text-body fw-bolder">{{ setting('app_name') ?: config('app.name') }}</h3> --}}
                </a>
              </div>
              <!-- /Logo -->
             
              <h4 class="mb-4 mt-4 text-center fs-5">Login</h4>                  
                                
                  <form wire:submit="login">                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" 
                              class="form-control @error('email') is-invalid @enderror" 
                              type="email" 
                              wire:model="email" 
                              value="{{ old('email') }}" 
                              required 
                              autofocus 
                              autocomplete="email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                
                    <div class="mb-3 form-password-toggle">
                      <div class="d-flex justify-content-between">
                        <label class="form-label" for="password">Password</label>
                        <a href="{{route('password.request')}}">
                          <small>Forgot Password?</small>
                        </a>
                      </div>
                        <div class="input-group input-group-merge">
                            <input class="form-control @error('password') is-invalid @enderror" 
                                  id="password" 
                                  type="password" 
                                  wire:model="password" 
                                  required>
                            <span class="input-group-text cursor-pointer" id="togglePassword">
                                <i class="bx bx-hide"></i>
                            </span>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                
                    {{-- Check if there is a throttle error --}}
                    @if (session('throttle'))
                        <div class="alert alert-danger">
                            {{ session('throttle') }}
                        </div>
                    @endif
                
                    <div class="mb-3">
                        <button class="btn btn-primary d-grid w-100" type="submit">Log in</button>
                    </div>
                  </form>
               
                <p class="text-center">
                  
                  <a wire:navigate href="{{route('register')}}"> or
                    <span>Create an account</span>
                  </a>
                </p>

                <script>
                  document.getElementById('showUserLoginBtn').addEventListener('click', function () {
                      document.getElementById('userLoginForm').classList.remove('d-none');
                      this.style.display = 'none';
                  });
                </script>
              
              
            </div>
          </div>
          <!-- /Register -->