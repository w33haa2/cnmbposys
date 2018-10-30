@extends('admin.default')

@section('content')
<!-- ### $App Screen Content ### --> 
<div class="full-container">
    <div class="email-app">
        <div class="email-side-nav remain-height">
            <div class="h-100 layers">
                <div class="p-20 bgc-white-100 layer w-100">
                    <div class="card" style='background-color:transparent;border:transparent'>
                      <div class="box">
                            <!-- an ID that has _P at the end means that it is for the profile of a specific person being outputted on the left -->
                            <div class="img">
                                <img id="profile-image-display" src="{{ !empty($profile->image)? $profile->image : '/images/nobody.jpg'}}">
                            </div>
                            <h6><span id="name_P">{{ $profile->firstname." ".$profile->middlename." ".$profile->lastname }}</span></h6>
                            <span class="maoni">
                                <span id="role_P">{{ $role->name }}</span>
                            </span>
                            <br>
                            <br>
                            <div class='container'>
                                <table class="profile-table" style='' width="100%">
                                    <tr >
                                        <td align='left'>Contact</td>
                                        <td align="right" id="contact_P">{{$profile->contact_number}}</td>
                                    </tr>
                                    <tr>
                                        <td align='left'>Email</td>
                                        <td align="right" id='email_P'>{{$user->email}}</td>
                                    </tr>
                                    <tr>
                                        <td align='left'>Address</td>
                                        <td align="right" id='address_P'>{{$profile->address}}</td>
                                    </tr>
                                    <tr>
                                        <td align='left' >SSS</td>
                                        <td align="right" id='sss_P'>{{$profile->benefits[0]->id_number}}</td>
                                    </tr>
                                    <tr>
                                        <td align='left'>PhilHealth</td>
                                        <td align="right" id='philhealth_P'>{{$profile->benefits[1]->id_number}}</td>
                                    </tr>
                                    <tr>
                                        <td align='left'>PagIbig</td>
                                        <td align="right" id='pagibig_P'>{{$profile->benefits[2]->id_number}}</td>
                                    </tr>
                                    <tr>
                                        <td align='left'>TIN</td>
                                        <td align="right" id='tin_P'>{{$profile->benefits[3]->id_number}}</td>
                                    </tr>
                                    <tr>
                                        <td align='left'>Birth Date</td>
                                        <td align="right" id='birth_P'>{{$profile->birthdate}}</td>
                                    </tr>
                                    <tr>
                                        <td align='left'>Hired Date</td>
                                        <td align="right" id='hired_P'>{{$profile->hired_date}}</td>
                                    </tr>
                                </table>
                                
                                
                            </div>
                            <!-- <div class='' style='border-left:5px solid yellow;padding:5px;'>
                                <div>contact</div>
                                <div>email</div>
                                <div>address</div>
                            </div> -->
                            <!-- <br>
                            <hr>
                            Email:&nbsp;<span id="email_P">{{ $user->email }}</span>
                            <br>
                            Contact:&nbsp;<span id="contact_P">{{ $profile->contact_number }}</span>
                            <br>
                            Address:&nbsp;<span id="address_P">{{ $profile->address }}</span>
                            <br>
                            <hr>
                            Birthday:&nbsp;<span id="birth_P"> {{ $profile->birthdate }}</span>
                            <br>
                            Date Hired:&nbsp;<span id="hired_P">{{ $profile->hired_date }}</span>
                            <br>
                            Gender:&nbsp;<span id="gender_P">{{ $profile->gender }}</span>
                            <br>
                            <hr>
                            SSS:&nbsp;<span id="sss_P">{{ !empty($profile->benefits[0]->id_number) ? $profile->benefits[0]->id_number : 'N/A' }}</span>
                            <br>
                            Philhealth:&nbsp;<span id="philhealth_P">{{ !empty($profile->benefits[1]->id_number) ? $profile->benefits[1]->id_number : 'N/A' }}</span>
                            <br>
                            Pag-ibig:&nbsp;<span id="pagibig_P">{{ !empty($profile->benefits[2]->id_number) ? $profile->benefits[2]->id_number : 'N/A' }}</span>
                            <br>
                            TIN:&nbsp;<span id="tin_P">{{ !empty($profile->benefits[3]->id_number) ? $profile->benefits[3]->id_number : 'N/A' }}</span>
                            <br> -->
                            <br>
                            @if(isAdmin())
                            <button type="submit" class="btn cur-p btn-dark form-action-button" id="profile-edit-button" data-portion="profile" data-action="edit" data-id="{{$profile->id}}"><span class="ti-pencil-alt" style='pointer:none;'></span> Edit</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="email-wrapper">
            <!-- Content -->
            
            <div class="bdT pX-40 pY-30 col-md-12">
                <h4 class="c-grey-900 mB-20">Employee list</h4> 

                <div class="form-group reposition">
                <div class="btn-group mR-2" role="group" aria-label="First group">
                    <div class="btn-group">
                        <button type="button cur-p ti-arrow-left" class="btn cur-p btn-primary" id="prevProfile" disabled>← Prev</button>
                    </div>

                    <div class="btn-group">
                        @if(isAdminHRM())
                        <button class="btn cur-p btn-primary" id="showAll"><span class="ti-menu"></span></button>
                        @endif
                        <button class="btn cur-p btn-primary" id="showChild"><span class="ti-menu-alt"></span></button>
                        <button class="btn cur-p btn-primary" id="showTerminated"><span class="ti-na"></span></button>
                    </div>

                    @if(isAdminHR())
                    <div class="btn-group">
                        <button type="input" class="btn cur-p btn-info excel-action-button"  id ="import" data-action="import"><span class="ti-upload"></span></button>
                        <button type="input" class="btn cur-p btn-info excel-action-button"  id ="export" data-action="export"><span class="ti-download"></span></button>
                    </div>

                    <button type="submit" class="btn cur-p btn-secondary form-action-button" id="addflag" data-action="add" data-url="/employee"><span class="ti-pencil-alt"></span></button>
                    @endif
                </div>
                </div>

                <table id="employee" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Employee No.</th>
                            <th>Picture</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Birthday</th>
                            <th>Gender</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Action</th> 
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
    @include('admin.dashboard.include.employee_form_modal');  

      <script>
        console.log({!! $emp !!})
      </script>
@endsection
