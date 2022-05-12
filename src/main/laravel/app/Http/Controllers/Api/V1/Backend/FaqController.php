<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqRequest;
use App\Models\Faq;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    public function index()
    {
        return success(Faq::sorted()->get());
    }

    public function store(FaqRequest $request)
    {
        $faq = Faq::create($request->validated());

        return success($faq, "Faq saved successfully");
    }

    public function show(Faq $faq)
    {
        return success($faq);
    }

    public function update(FaqRequest $request, Faq $faq)
    {
        $faq->update($request->validated());

        return success(null, "Faq updated successfully");
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return success(null, "Faq deleted successfully");
    }

    public function sort()
    {
        $ids = request()->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['numeric', 'exists:faqs,id'],
        ])['ids'];

        DB::transaction(function () use ($ids) {
            foreach ($ids as $idx => $id) {
                Faq::where("id", $id)
                    ->where(
                        fn ($q) => $q->where("position", "!=", $idx + 1)
                            ->orWhere("position", "=", null)
                    )
                    ->update([
                        "position" => $idx + 1
                    ]);
            }
        });

        return success("Sort saved successfully");
    }
}
