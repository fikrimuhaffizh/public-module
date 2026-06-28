<?php

namespace Modules\Public\app\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\Public\app\Http\Requests\ClientRequest;
use Modules\Public\app\Http\Requests\ReorderRequest;
use Modules\Public\app\Models\Client;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.client.view')->only('index');
        $this->middleware('permission:public.cms.client.create')->only(['create', 'store']);
        $this->middleware('permission:public.cms.client.update')->only(['edit', 'update', 'reorder']);
        $this->middleware('permission:public.cms.client.delete')->only('destroy');
    }

    public function index()
    {
        return view('public::pages.cms.section.client.index', [
            'clients' => Client::orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.section.client.create-edit-ajax', ['client' => new Client]);
    }

    public function store(ClientRequest $request)
    {
        $data = Arr::except($request->validated(), ['logo']);
        $data['sort_order'] = (int) Client::max('sort_order') + 1;
        $client = Client::create($data);

        if ($request->hasFile('logo')) {
            $client->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        return jsonSuccess('Klien berhasil ditambahkan.', route('cms.client.index'));
    }

    public function edit(Client $client)
    {
        return view('public::pages.cms.section.client.create-edit-ajax', compact('client'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        $client->update(Arr::except($request->validated(), ['logo']));

        if ($request->hasFile('logo')) {
            $client->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        return jsonSuccess('Klien berhasil diperbarui.', route('cms.client.index'));
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return jsonSuccess('Klien berhasil dihapus.');
    }

    public function reorder(ReorderRequest $request)
    {
        foreach ($request->validated()['order'] ?? [] as $index => $encryptedId) {
            $id = decryptIdIfEncrypted($encryptedId, false);
            Client::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        return jsonSuccess('Urutan klien berhasil diperbarui.');
    }
}
