<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flower Marketing Visit Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        .container {
            padding: 1rem;
            max-width: 600px;
            margin: auto;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 1rem;
        }

        input, select {
            padding: 0.5rem;
            font-size: 1rem;
            margin-top: 0.3rem;
        }

        .checkbox-group {
            display: flex;
            gap: 1rem;
            margin-top: 0.3rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        button {
            margin-top: 1.5rem;
            padding: 0.7rem;
            font-size: 1rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        @media (max-width: 600px) {
            .container {
                padding: 1rem;
                width: 100%;
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
            <label for="locationType">Location Type</label>
            <select id="locationType" name="locationType">
                <option value="apartment">Apartment</option>
                <option value="individual">Individual</option>
                <option value="temple">Temple</option>
                <option value="business">Business</option>
            </select>

            <label for="datetime">Date and Time</label>
            <input type="datetime-local" id="datetime" name="datetime">

            <label for="contactName">Contact Person Name</label>
            <input type="text" id="contactName" name="contactName">

            <label for="contactNumber">Contact Person Number</label>
            <input type="tel" id="contactNumber" name="contactNumber">

            <label for="noOfApartments">Number of Apartments</label>
            <input type="number" id="noOfApartments" name="noOfApartments">

            <label>Already Delivered?</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="delivered" value="yes"> Yes</label>
                <label><input type="checkbox" name="delivered" value="no"> No</label>
            </div>

            <label for="apartmentName">Apartment Name</label>
            <input type="text" id="apartmentName" name="apartmentName">

            <label for="apartmentNumber">Apartment Number</label>
            <input type="text" id="apartmentNumber" name="apartmentNumber">

            <label for="locality">Locality</label>
            <input type="text" id="locality" name="locality">

            <label for="landmark">Landmark</label>
            <input type="text" id="landmark" name="landmark">

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
