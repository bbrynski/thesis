import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Gender } from '../shared/gender';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class GenderService {
  private urlGender = 'http://localhost/repository/pracainz/PracaInzBackend/Plec';
  constructor(private http: HttpClient) { }

  getAllGender(): Observable<Gender[]> {
    return this.http.get<Gender[]>(this.urlGender);
  }
}
