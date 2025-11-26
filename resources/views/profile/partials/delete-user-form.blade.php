<div class="mb-3 col-12 mb-0">
    <div class="alert alert-warning">
      <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?</h6>
      <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
    </div>
  </div>
    <form method="POST" action="{{ route('profile.destroy') }}" id="formAccountDeactivation">
      @csrf
      @method('DELETE')
  
      <div class="form-check mb-3">
          <input
              class="form-check-input"
              type="checkbox"
              name="accountActivation"
              id="accountActivation"
              required
          />
          <label class="form-check-label" for="accountActivation">
              I confirm my account deactivation
          </label>
      </div>
  
      <div class="mb-3">
          <label for="password" class="form-label">Confirm Password</label>
          <input
              type="password"
              class="form-control @error('userDeletion.password') is-invalid @enderror"
              id="password"
              name="password"
              placeholder="Enter your password"
              required
          >
          @error('userDeletion.password')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
      </div>
  
      <button type="submit" class="btn btn-danger deactivate-account">Deactivate Account</button>
    </form>