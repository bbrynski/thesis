import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Boots } from '../shared/boots';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class BootsService {
  private isEditMode = false;
  private toEditBootsPack = {
    boots: null,
    amount: null
  };
  private bootsUrl = 'http://localhost/repository/pracainz/PracaInzBackend/Buty';

  constructor(private http: HttpClient) { }

  getByDate(value: any): Observable<any[]> {
    return this.http.post<any[]>(this.bootsUrl + '/data', value, httpOptions);
  }


  getAllBoots(single: boolean): Observable<any[]> {
    if (single) {
      return this.http.get<any[]>(this.bootsUrl + '/pojedyncze');
    } else {
      return this.http.get<any[]>(this.bootsUrl);
    }
  }

  getOneBoots(index: number): Observable<any> {
    return this.http.get<any[]>(this.bootsUrl + '/' + index);
  }

  addBoots(boots: any, isEditMode: boolean): Observable<any> {
    if (isEditMode) {
      return this.http.post<any>(this.bootsUrl + '/edytuj', boots, httpOptions);
    } else {
      return this.http.post<any>(this.bootsUrl + '/wstaw', boots, httpOptions);
    }
  }

  deleteBoots(index: number): Observable<any[]> {
    return this.http.delete<any[]>(this.bootsUrl + '/usun/' + index);
  }

  changeEditMode() {
    this.isEditMode = !this.isEditMode;
  }
  getIsEditMode() {
    return this.isEditMode;
  }
  setEditBoots(boots: Boots, amount: number) {
    this.toEditBootsPack.boots = boots;
    this.toEditBootsPack.amount = amount;
    this.changeEditMode();
  }
  getEditBoots() {
    return this.toEditBootsPack;
  }
}
