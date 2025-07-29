<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flower Marketing Visit Form</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e9f0f4;
        }

        header {
            background-color: #1E88E5;
            color: white;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        label {
            margin-bottom: 0.3rem;
            font-weight: 600;
        }

        .form-group i {
            position: absolute;
            top: 2.5rem;
            left: 0.8rem;
            color: #1E88E5;
        }

        input, select {
            padding: 0.7rem 0.7rem 0.7rem 2.5rem;
            font-size: 1rem;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        button {
            padding: 0.9rem;
            font-size: 1rem;
            background-color: #1E88E5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            grid-column: 1 / -1;
        }

        button:hover {
            background-color: #1565C0;
        }

        @media (max-width: 600px) {
            .container {
                padding: 1rem;
                margin: 1rem;
            }

            form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Flower Marketing Visit Form</h1>
    </header>
    <div class="container">
        <form>
            <div class="form-group">
                <label for="locationType">Location Type</label>
                <i class="fas fa-map-marker-alt"></i>
                <select id="locationType" name="locationType">
                    <option value="apartment">Apartment</option>
                    <option value="individual">Individual</option>
                    <option value="temple">Temple</option>
                    <option value="business">Business</option>
                </select>
            </div>

            <div class="form-group">
                <label for="datetime">Date and Time</label>
                <i class="fas fa-calendar-alt"></i>
                <input type="datetime-local" id="datetime" name="datetime">
            </div>

            <div class="form-group">
                <label for="contactName">Contact Person Name</label>
                <i class="fas fa-user"></i>
                <input type="text" id="contactName" name="contactName">
            </div>

            <div class="form-group">
                <label for="contactNumber">Contact Person Number</label>
                <i class="fas fa-phone"></i>
                <input type="tel" id="contactNumber" name="contactNumber">
            </div>

            <div class="form-group">
                <label for="noOfApartments">Number of Apartments</label>
                <i class="fas fa-building"></i>
                <input type="number" id="noOfApartments" name="noOfApartments">
            </div>

            <div class="form-group">
                <label>Already Delivered?</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="delivered" value="yes"> Yes</label>
                    <label><input type="checkbox" name="delivered" value="no"> No</label>
                </div>
            </div>

            <div class="form-group">
                <label for="apartmentName">Apartment Name</label>
                <i class="fas fa-building"></i>
                <input type="text" id="apartmentName" name="apartmentName">
            </div>

            <div class="form-group">
                <label for="apartmentNumber">Apartment Number</label>
                <i class="fas fa-hashtag"></i>
                <input type="text" id="apartmentNumber" name="apartmentNumber">
            </div>

            <div class="form-group">
                <label for="locality">Locality</label>
                <i class="fas fa-city"></i>
                <input type="text" id="locality" name="locality">
            </div>

            <div class="form-group">
                <label for="landmark">Landmark</label>
                <i class="fas fa-map-signs"></i>
                <input type="text" id="landmark" name="landmark">
            </div>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
