<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use App\Models\loanaff;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


class PDFController extends Controller
{
    // public function generatePDF()
    // {
    //     $loan = loanaff::all();

    //     // Your HTML content
    //     $htmlContent = View::make('index', compact('loan'))->render();
    //     // dd($htmlContent);

    //     // Instantiate dompdf
    //     $dompdf = new Dompdf();

    //     // Load HTML content
    //     $dompdf->loadHtml($htmlContent);

    //     // Render PDF
    //     $dompdf->render();

    //     // Define file path
    //     $filePath = 'pdfs/your_file_name.pdf';

    //     // Store PDF
    //     Storage::put($filePath, $dompdf->output());
    //     // $dompdf->set_option('debugKeepTemp', true);
    //     // $dompdf->set_option('isHtml5ParserEnabled', true);


    //     // Optionally, you can return the file path or a response
    //     return redirect('cal')->with([
    //         'message' => 'PDF generated successfully!',
    //         'alert-type' => 'success',
    //         'filePath' => $filePath,
    //     ]);
    // }

    public function movePDF(Request $request)
    {
        // Get the home directory based on the operating system
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        } else {
            // Unix-like systems (Linux, macOS)
            $homeDirectory = getenv('HOME');
        }

        // Construct the path to the Downloads directory
        $downloadDirectory = $homeDirectory . DIRECTORY_SEPARATOR . 'Downloads';
        $downloadFilePath = $downloadDirectory . DIRECTORY_SEPARATOR . 'LoanCal.pdf';

        // Check if the file exists in the download directory
        if (!file_exists($downloadFilePath)) {
            return response()->json(['error' => 'PDF file not found in the download directory.'], 404);
        }

        // Generate a unique filename to avoid conflicts
        $filename = 'LoanCal_' . uniqid() . '.pdf';

        // Get the base storage path from the environment variable
        $destinationFolder = storage_path('app/pdfs');
        // dd($destinationFolder);

        // Ensure the destination folder exists
        if (!is_dir($destinationFolder)) {
            if (!mkdir($destinationFolder, 0777, true)) {
                return response()->json(['error' => 'Error creating destination folder.'], 500);
            }
        }

        // Define the destination file path
        $destinationFilePath = $destinationFolder . DIRECTORY_SEPARATOR . $filename;

        // Move the file to the destination folder
        if (rename($downloadFilePath, $destinationFilePath)) {
            return response()->json(['success' => true, 'filename' => $filename]);
        } else {
            return response()->json(['error' => 'Error moving PDF file.'], 500);
        }
    }
}
