import { Injectable } from '@angular/core';
import { Snowboard } from '../shared/snowboard';
import { SnowboardLeg } from '../shared/snowboard-leg';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class SnowboardService {
  private isEditMode = false;
  private toEditSnowboardPack = {
    snowboard: null,
    amount: null
  };
  private urlSnowboard = 'http://localhost/repository/pracainz/PracaInzBackend/Snowboard';
  private urlSnowboardAdd = 'http://localhost/repository/pracainz/PracaInzBackend/Snowboard/wstaw';

  constructor(private http: HttpClient) { }

  getByDate(value: any): Observable<any[]> {
    return this.http.post<any[]>(this.urlSnowboard + '/data', value, httpOptions);
  }

  getAllSnowboard(single: boolean): Observable<any[]> {
    if (single) {
      return this.http.get<any[]>(this.urlSnowboard + '/pojedyncze', httpOptions);
    } else {
      return this.http.get<any[]>(this.urlSnowboard, httpOptions);
    }
  }

  getSnowboardLeg(): Observable<SnowboardLeg[]> {
    return this.http.get<SnowboardLeg[]>(this.urlSnowboard + '/ustawienie', httpOptions);
  }

  addSnowboard(snowboard: any, isEditMode): Observable<any> {
    if (isEditMode) {
      return this.http.post<any>(this.urlSnowboard + '/edytuj', snowboard, httpOptions);
    } else {
      return this.http.post<any>(this.urlSnowboardAdd, snowboard, httpOptions);
    }
  }
  editSnowboard(snowboard: any): Observable<any[]> {
    return this.http.post<any>(this.urlSnowboardAdd, snowboard, httpOptions);
  }

  deleteSnowboard(index: number): Observable<any[]> {
    return this.http.delete<any[]>(this.urlSnowboard + '/usun/' + index);
  }

  getOneSnowboard(index: number): Observable<any> {
    return this.http.get<any[]>(this.urlSnowboard + '/' + index);
  }
  changeEditMode() {
    this.isEditMode = !this.isEditMode;
  }
  getIsEditMode() {
    return this.isEditMode;
  }
  setEditSnowboard(snowboard: Snowboard, amount: number) {
    this.toEditSnowboardPack.snowboard = snowboard;
    this.toEditSnowboardPack.amount = amount;
    this.changeEditMode();
  }
  getEditSnowboard() {
    return this.toEditSnowboardPack;
  }

}
