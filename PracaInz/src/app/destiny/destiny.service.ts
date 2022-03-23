import { Injectable } from '@angular/core';
import { Type } from '../shared/destiny';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class DestinyService {
  private urlDestiny = 'http://localhost/repository/pracainz/PracaInzBackend/Przeznaczenie';

  constructor(private http: HttpClient) { }

  getAllDestiny(): Observable<Type[]> {
    return this.http.get<Type[]>(this.urlDestiny);
  }
}
