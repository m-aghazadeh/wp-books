import React from "react";
import type {Book} from "@/shared/types";
import {Button} from "@/components/ui/button";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table";

export function BooksTable({
                               rows,
                               onEdit,
                               onDelete,
                           }: {
    rows: Book[];
    onEdit: (b: Book) => void;
    onDelete: (id: number) => void;
}) {
    return (
        <div className="rounded border">
            <Table dir="rtl">

                <TableHeader> <TableRow className="">
                    <TableHead className="w-[45%] text-right">عنوان</TableHead>
                    <TableHead className="w-[35%] text-right">نویسنده</TableHead>
                    <TableHead className="w-[10%] text-right">سال انتشار</TableHead>
                    <TableHead className="w-[10%] text-right">عملیات</TableHead> </TableRow> </TableHeader>


                <TableBody>
                    {rows.map((b) => (
                        <TableRow key={b.id}>

                            <TableCell>{b.title}</TableCell> <TableCell>{b.author}</TableCell>
                            <TableCell>{b.published_year}</TableCell>
                            <TableCell className="text-right">
                                <div className="inline-flex gap-2">
                                    <Button variant="outline" onClick={() => onEdit(b)}>ویرایش</Button>
                                    <Button variant="destructive" onClick={() => onDelete(b.id)}>حذف</Button>
                                </div>
                            </TableCell> </TableRow>
                    ))} {rows.length === 0 && (
                    <TableRow>
                        <TableCell colSpan={4} className="text-center text-sm text-muted-foreground py-8"> کتابی یافت نشد </TableCell>
                    </TableRow>
                )}
                </TableBody> </Table>
        </div>
    );
}
