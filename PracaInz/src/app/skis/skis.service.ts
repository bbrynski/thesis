import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Skis } from '../shared/skis';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class SkisService {
  private isEditMode = false;
  private toEditSkisPack = {
    skis: null,
    amount: null
  };

  private skisUrl = 'http://localhost/repository/pracainz/PracaInzBackend/Narty';
  constructor(private http: HttpClient) { }


  getByDate(value: any): Observable<any[]> {
    return this.http.post<any[]>(this.skisUrl + '/data', value, httpOptions);
  }

  getAllSkis(single: boolean): Observable<any[]> {
    if (single) {
      return this.http.get<any[]>(this.skisUrl + '/pojedyncze');
    } else {
      return this.http.get<any[]>(this.skisUrl);
    }
  }

  getOneSkis(index: number): Observable<any> {
    return this.http.get<any[]>(this.skisUrl + '/' + index);
  }

  addSkis(skis: any, isEditMode: boolean): Observable<any> {
    if (isEditMode) {
      return this.http.post<any>(this.skisUrl + '/edytuj', skis, httpOptions);
    } else {
      return this.http.post<any>(this.skisUrl + '/wstaw', skis, httpOptions);
    }
  }

  deleteSkis(index: number): Observable<any[]> {
    return this.http.delete<any[]>(this.skisUrl + '/usun/' + index);
  }

  changeEditMode() {
    this.isEditMode = !this.isEditMode;
  }
  getIsEditMode() {
    return this.isEditMode;
  }
  setEditSkis(skis: Skis, amount: number) {
    this.toEditSkisPack.skis = skis;
    this.toEditSkisPack.amount = amount;
    this.changeEditMode();
  }
  getEditSkis() {
    return this.toEditSkisPack;
  }
}
