@extends('layouts.app')
@section('pageTitle', 'Upload Resume')

@section('breadcrumbs', Breadcrumbs::render('resume'))

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        h1 {
            font-family: 'Roboto', sans-serif;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .resume-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .small-text {
            font-size: 12px;
            color: #808080;
        }

        .resume-info {
            display: flex;
            flex-direction: row;
            flex-grow: 1;
        }

        .resume-cards {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .resume-card {
            flex: 0 0 calc(50% - 10px); /* Two cards per row with some spacing */
            margin-bottom: 20px;
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 10px;
            max-height: 180px;
        }

        .card-title a {
            text-decoration: none;
            color: #333; /* Link color */
        }
        .resume-wrapper{
            display: flex;
            flex-direction: column;
        }

        .delete-resume-button {
        background: none;
        border: none;
        cursor: pointer;
        }

        .delete-resume-button i {
            color: #f44336; /* Red color for the bin icon */
        }

        .upload-resume-form {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 20px;
        }

        /* Style for the "Choose a resume" input */
        .file-input {
            display: none; /* Hide the default input element */
        }

        .custom-file-upload {
            display: inline-block;
            padding: 10px 20px;
            background: #337ab7;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, color 0.3s;
        }

        .custom-file-upload:hover {
            background: #235a9b; 
        }

        /* Style for the "Upload resume" button */
        .upload-button {
            background: #4caf50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, color 0.3s;
        }

        .upload-button:hover {
            background: #45a049; 
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="resume-container">
            <h1>Your Resume</h1>
            <p class="small-text"  style="margin-bottom: 30px;">Upload your resume here. Supported file type is .pdf.</p>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif
            @error('resume')
                <div class="alert alert-warning">{{ $message }}</div>
            @enderror
            <ul>
                @if ($trainee->resume_path == null)
                    <p>No resume uploaded yet.</p>
                @else
                <ul class="resume-cards">
                    <li class="resume-card">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ asset($trainee->resume_path) }}" target="_blank" class="resume-link" style="color: blue;">
                                        {{ pathinfo($trainee->resume_path, PATHINFO_BASENAME) }}
                                    </a>
                                </h5>
                                    <button type="submit" class="delete-resume-button" data-toggle="modal" data-target="#confirmationModal">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>

                                <div class="modal" tabindex="-1" role="dialog" id="confirmationModal">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmation</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this resume?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <form action="{{ route('resumes.destroyResume', $trainee) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Confirm Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </li>
                </ul>          
                @endif
                <form action="/upload" method="POST" enctype="multipart/form-data" class="upload-resume-form">
                    @csrf
                    <input type="file" name="resume" id="resume" accept=".pdf" class="file-input" onchange="updateFileName()">
                    <div class="resume-info">
                        <label for="resume" class="custom-file-upload" style="margin-right: 30px;">Choose a resume</label>
                        <span id="file-name" class="file-name" style="margin-top: 10px;">No file selected</span>
                    </div>
                    <button type="submit" class="upload-button" style="margin-top: 30px;">Upload resume</button>
                </form>
            </ul>
        </div>
    </div>
</body>
<script>
    function updateFileName() {
        const fileInput = document.getElementById('resume');
        const fileName = document.getElementById('file-name');

        if (fileInput.files.length > 0) {
            fileName.textContent = fileInput.files[0].name;
        } else {
            fileName.textContent = 'No file selected';
        }
    }
</script>
@endsection