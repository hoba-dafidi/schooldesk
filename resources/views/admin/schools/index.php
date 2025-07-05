@extends('layouts/contentNavbarLayout')

@section('title', 'Manage Schools')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4 d-flex justify-content-between align-items-center">
    Manage Schools
    <button class="btn btn-primary" onclick="openCreateSchoolModal()">
      <i class="bx bx-plus"></i> Create New School
    </button>
  </h4>

  <!-- Alert Notifications -->
  <div id="schoolAlert" class="alert d-none" role="alert"></div>

  <!-- Schools List -->
  <div class="card">
    <div class="card-datatable table-responsive">
      <table class="table table-bordered datatables-basic">
        <thead>
          <tr>
            <th>#</th>
            <th>Logo</th>
            <th>Name</th>
            <th>Subdomain</th>
            <th>Custom Domain</th>
            <th>Status</th>
            <th>Paid Plan</th>
            <th>Phone</th>
            <th>Email</th>
            <th>City</th>
            <th>State</th>
            <th>Country</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($schools as $school)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
              @if($school->logo)
                <a href="{{ asset('storage/' . $school->logo) }}" target="_blank">
                  <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="rounded cursor-pointer" style="height: 40px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.3)'" onmouseout="this.style.transform='scale(1)'">
                </a>
              @else
                <span class="text-muted">No Logo</span>
              @endif
            </td>
            <td>{{ $school->name }}</td>
            <td>{{ $school->subdomain }}</td>
            <td>{{ $school->custom_domain }}</td>
            <td><span class="badge bg-label-{{ $school->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($school->status) }}</span></td>
            <td><span class="badge bg-label-{{ $school->is_paid_plan ? 'info' : 'warning' }}">{{ $school->is_paid_plan ? 'Yes' : 'No' }}</span></td>
            <td>{{ $school->phone }}</td>
            <td>{{ $school->email }}</td>
            <td>{{ $school->city }}</td>
            <td>{{ $school->state }}</td>
            <td>{{ $school->country }}</td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="openEditSchoolModal(@json($school))"><i class="bx bx-edit"></i></button>
              <form action="{{ route('schools.destroy', $school->id) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Edit/Create School Modal -->
     <div class="modal fade" id="schoolModal" tabindex="-1" aria-labelledby="schoolModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="schoolForm" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="schoolModalLabel">Create/Edit School</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body row g-4">
          <input type="hidden" name="id" id="school_id">

          <div class="col-md-6">
            <label for="name" class="form-label">School Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>

          <div class="col-md-6">
            <label for="subdomain" class="form-label">Subdomain</label>
            <input type="text" class="form-control" id="subdomain" name="subdomain" required>
          </div>

          <div class="col-md-6">
            <label for="custom_domain" class="form-label">Custom Domain</label>
            <input type="text" class="form-control" id="custom_domain" name="custom_domain">
          </div>

          <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="col-md-6">
            <label for="is_paid_plan" class="form-label">Paid Plan</label>
            <select id="is_paid_plan" name="is_paid_plan" class="form-select">
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
          </div>

          <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>

          <div class="col-md-6">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone">
          </div>

          <div class="col-md-6">
            <label for="type" class="form-label">School Type</label>
            <input type="text" class="form-control" id="type" name="type">
          </div>

          <div class="col-md-6">
            <label for="established_year" class="form-label">Established Year</label>
            <input type="number" class="form-control" id="established_year" name="established_year">
          </div>

          <div class="col-md-6">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address">
          </div>

          <div class="col-md-4">
            <label for="city" class="form-label">City</label>
            <input type="text" class="form-control" id="city" name="city">
          </div>

          <div class="col-md-4">
            <label for="state" class="form-label">State</label>
            <select class="form-select" id="state" name="state"></select>
          </div>

          <div class="col-md-4">
            <label for="country" class="form-label">Country</label>
            <select class="form-select" id="country" name="country"></select>
          </div>

          <div class="col-md-12">
            <label for="about" class="form-label">About</label>
            <textarea class="form-control" id="about" name="about" rows="3"></textarea>
          </div>

          <div class="col-md-6">
            <label for="logo" class="form-label">Logo</label>
            <input class="form-control" type="file" name="logo" id="logo" accept="image/*">
            <small class="text-muted">Max 2MB. JPG/PNG only.</small>
          </div>

          <div class="col-md-6">
            <label class="form-label">Preview</label>
            <div>
              <img id="logoPreview" src="#" alt="Logo Preview" style="max-height: 100px; display: none;" class="border rounded">
              <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeLogoBtn" style="display:none">Remove Logo</button>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

</div>

<!-- Loading Spinner -->
<div id="loadingSpinner" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-light bg-opacity-50 d-none" style="z-index:1055">
  <div class="spinner-border text-primary" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
</div>

@endsection

@push('scripts')
<script>
  function showLoading() {
    document.getElementById('loadingSpinner').classList.remove('d-none');
  }

  function hideLoading() {
    document.getElementById('loadingSpinner').classList.add('d-none');
  }

  function showAlert(message, type = 'success') {
    const alertBox = document.getElementById('schoolAlert');
    alertBox.className = `alert alert-${type}`;
    alertBox.textContent = message;
    alertBox.classList.remove('d-none');
    setTimeout(() => alertBox.classList.add('d-none'), 4000);
  }

  document.getElementById('logo').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file && file.size < 2097152 && ['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const preview = document.getElementById('logoPreview');
        preview.src = e.target.result;
        preview.style.display = 'block';
        document.getElementById('removeLogoBtn').style.display = 'inline-block';
      }
      reader.readAsDataURL(file);
    } else {
      alert('Invalid image. Max size is 2MB. Only JPG and PNG formats allowed.');
      e.target.value = '';
    }
  });

  document.getElementById('removeLogoBtn').addEventListener('click', function () {
    const input = document.getElementById('logo');
    input.value = '';
    document.getElementById('logoPreview').style.display = 'none';
    this.style.display = 'none';
  });

  function openCreateSchoolModal() {
    document.getElementById('schoolForm').reset();
    document.getElementById('school_id').value = '';
    document.getElementById('logoPreview').style.display = 'none';
    document.getElementById('removeLogoBtn').style.display = 'none';
    new bootstrap.Modal(document.getElementById('schoolModal')).show();
  }

  function openEditSchoolModal(school) {
    const form = document.getElementById('schoolForm');
    for (const key in school) {
      if (form.elements[key]) form.elements[key].value = school[key];
    }

    if (school.logo) {
      const logoPath = `/storage/${school.logo}`;
      const preview = document.getElementById('logoPreview');
      preview.src = logoPath;
      preview.style.display = 'block';
      document.getElementById('removeLogoBtn').style.display = 'inline-block';
    }

    new bootstrap.Modal(document.getElementById('schoolModal')).show();
  }
</script>
@endpush
