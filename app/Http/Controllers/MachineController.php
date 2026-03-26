<?php
//////////////////////////////////////////code without "-" between names in QR
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Machine;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;

// class MachineController extends Controller
// {
//     // List all machines
//     public function index()
//     {
//         return response()->json(Machine::all());
//     }

//     // Show machine by name
//     public function show($name)
//     {
//         $decodedName = urldecode($name);
//         $machine = Machine::where('name', $decodedName)->firstOrFail();
//         return response()->json($machine);
//     }

//     // Store machine and generate QR (QR encodes ONLY the machine name)
//     public function store(Request $request)
//     {
//         $data = $request->validate([
//             'name'         => 'required|string|max:255|unique:machines,name',
//             'description'  => 'nullable|string',
//             'image_url'    => 'nullable|string|max:2048',
//             'tutorial_url' => 'nullable|url|max:2048',
//         ]);

//         $machine = Machine::create($data);

//         // The QR should encode only the machine name
//         $qrText = $machine->name;

//         // Generate SVG QR
//         $ext = 'svg';
//         $binary = QrCode::format('svg')->size(400)->generate($qrText);

//         $dir = public_path('qr');
//         if (!is_dir($dir)) {
//             mkdir($dir, 0755, true);
//         }

//         $filename = "qr-" . preg_replace('/\s+/', '_', strtolower($machine->name)) . "-" . time() . ".$ext";
//         file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $binary);

//         $machine->qr_code_path = "qr/$filename";
//         $machine->save();

//         return response()->json([
//             'message' => 'Machine created with QR',
//             'machine' => $machine,
//             'qr_url'  => asset("qr/$filename"),
//             'qr_text' => $qrText, // this is what the QR actually contains
//         ], 201);
//     }

//     // Update machine
//     public function update(Request $request, Machine $machine)
//     {
//         $data = $request->validate([
//             'name'         => 'sometimes|string|max:255|unique:machines,name,' . $machine->id,
//             'description'  => 'sometimes|nullable|string',
//             'image_url'    => 'sometimes|nullable|string|max:2048',
//             'tutorial_url' => 'sometimes|nullable|url|max:2048',
//         ]);

//         $machine->update($data);

//         return response()->json([
//             'message' => 'Machine updated',
//             'machine' => $machine,
//         ]);
//     }

//     // Generate QR for existing machine (QR encodes ONLY the machine name)
//     public function generateQr($name)
//     {
//         $decodedName = urldecode($name);
//         $machine = Machine::where('name', $decodedName)->firstOrFail();

//         $qrText = $machine->name;

//         $ext = 'svg';
//         $binary = QrCode::format('svg')->size(400)->generate($qrText);

//         $dir = public_path('qr');
//         if (!is_dir($dir)) {
//             mkdir($dir, 0755, true);
//         }

//         $filename = "qr-" . preg_replace('/\s+/', '_', strtolower($machine->name)) . "-" . time() . ".$ext";
//         file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $binary);

//         $machine->qr_code_path = "qr/$filename";
//         $machine->save();

//         return response()->json([
//             'message' => 'QR generated',
//             'qr_path' => asset("qr/$filename"),
//             'qr_text' => $qrText, // what the QR actually contains
//             'format'  => $ext,
//         ]);
//     }
// }














//////////////////////////////////////////code with "-" between names in QR




namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MachineController extends Controller
{
    // List all machines
    public function index()
    {
        return response()->json(Machine::all());
    }

    // Show machine by name
    public function show($name)
    {
        // Convert dash back to spaces when searching
        $decodedName = str_replace('-', ' ', $name);

        $machine = Machine::where('name', $decodedName)->firstOrFail();

        return response()->json($machine);
    }

    // Store machine and generate QR
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255|unique:machines,name',
            'description'  => 'nullable|string',
            'image_url'    => 'nullable|string|max:2048',
            'tutorial_url' => 'nullable|url|max:2048',
        ]);

        $machine = Machine::create($data);

        // Convert spaces to dash ONLY for QR text
        $qrText = str_replace(' ', '-', $machine->name);

        // Generate SVG QR
        $ext = 'svg';
        $binary = QrCode::format('svg')->size(400)->generate($qrText);

        $dir = public_path('qr');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = "qr-" . preg_replace('/\s+/', '_', strtolower($machine->name)) . "-" . time() . ".$ext";

        file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $binary);

        $machine->qr_code_path = "qr/$filename";
        $machine->save();

        return response()->json([
            'message' => 'Machine created with QR',
            'machine' => $machine,
            'qr_url'  => asset("qr/$filename"),
            'qr_text' => $qrText
        ], 201);
    }

    // Update machine
    public function update(Request $request, Machine $machine)
    {
        $data = $request->validate([
            'name'         => 'sometimes|string|max:255|unique:machines,name,' . $machine->id,
            'description'  => 'sometimes|nullable|string',
            'image_url'    => 'sometimes|nullable|string|max:2048',
            'tutorial_url' => 'sometimes|nullable|url|max:2048',
        ]);

        $machine->update($data);

        return response()->json([
            'message' => 'Machine updated',
            'machine' => $machine,
        ]);
    }

    // Generate QR for existing machine
    public function generateQr($name)
    {
        $decodedName = str_replace('-', ' ', $name);

        $machine = Machine::where('name', $decodedName)->firstOrFail();

        // Replace spaces with dash for QR
        $qrText = str_replace(' ', '-', $machine->name);

        $ext = 'svg';
        $binary = QrCode::format('svg')->size(400)->generate($qrText);

        $dir = public_path('qr');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = "qr-" . preg_replace('/\s+/', '_', strtolower($machine->name)) . "-" . time() . ".$ext";

        file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $binary);

        $machine->qr_code_path = "qr/$filename";
        $machine->save();

        return response()->json([
            'message' => 'QR generated',
            'qr_path' => asset("qr/$filename"),
            'qr_text' => $qrText,
            'format'  => $ext,
        ]);
    }
}