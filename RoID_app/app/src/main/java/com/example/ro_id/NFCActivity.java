package com.example.ro_id;

import androidx.annotation.RequiresApi;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.ContextCompat;
import androidx.preference.PreferenceManager;

import android.app.PendingIntent;
import android.content.Intent;
import android.content.SharedPreferences;
import android.nfc.Tag;
import android.os.Build;
import android.os.Bundle;
import android.os.StrictMode;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.TextView;

import java.io.BufferedInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.Arrays;
import java.util.Base64;

import okhttp3.FormBody;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;
import okio.BufferedSink;

import android.nfc.tech.IsoDep;
import android.nfc.NfcAdapter;



@RequiresApi(api = Build.VERSION_CODES.KITKAT)
public class NFCActivity extends AppCompatActivity implements NfcAdapter.ReaderCallback {

    String name, att_id, challenge, signature, certificate, json;
    NfcAdapter nfcAdapter;
    String pin;
    Button btn_sample;
    TextView label;
    final OkHttpClient client = new OkHttpClient().newBuilder().build();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_nfcactivity);
        Window window = this.getWindow();
        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.setStatusBarColor(ContextCompat.getColor(this, R.color.white));
        }
        if (Build.VERSION.SDK_INT > 9) {
            StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
            StrictMode.setThreadPolicy(policy);
        }
        Bundle bundle = getIntent().getExtras();
        att_id = bundle.getString("att_id");
        pin = bundle.getString("pin");
        challenge = bundle.getString("challenge");
        SharedPreferences sharedPreferences =
                PreferenceManager.getDefaultSharedPreferences(this /* Activity context */);
        name = sharedPreferences.getString("roid_address", "");
        if (name == "") {
            name = "10.15.11.233";
        }
        btn_sample = findViewById(R.id.button5);
        String finalName = name;
        btn_sample.setOnClickListener(v -> {
            try {
                String url = "http://" + finalName + ":8080/get_code?att_id=" + att_id;
                putData(finalName, att_id);
                finish();
            } catch (Exception e) {
                abortLogin(finalName, att_id);
                finish();
                return;
            }
        });
        label = findViewById(R.id.textView10);
        nfcAdapter = NfcAdapter.getDefaultAdapter(this);
        //nfcAdapter.enableReaderMode(this, this, NfcAdapter.FLAG_READER_SKIP_NDEF_CHECK, null);
    }

    @Override
    public void onResume()
    {
        super.onResume();
        nfcAdapter.enableReaderMode(this, this, NfcAdapter.FLAG_READER_SKIP_NDEF_CHECK, null);

    }

    @Override
    public void onPause()
    {
        super.onPause();
        nfcAdapter.disableReaderMode(this);

    }

    private void abortLogin(String addr, String attempt_id) {
        String urlString = "http://" + addr + ":8080/abort?att_id=" + attempt_id;


        //Log.d("DEBUG", "HTTP Request");
        HttpURLConnection urlConnection;
        String response = "";
        try {
            URL url = new URL(urlString);
            urlConnection = (HttpURLConnection) url.openConnection();
            InputStream in = new BufferedInputStream(urlConnection.getInputStream());
            response = readStream(in);
            //Log.d("DEBUG", response);
        } catch (Exception e) {
            System.out.println(e.getMessage());
        }

        return;
    }

    void putData(String addr, String attempt_id) throws IOException {
        String url = "http://" + addr + ":8080/give_sample?att_id=" + attempt_id;
        Request request = new Request.Builder()
                .url(url)
                .build();

        try (Response response = client.newCall(request).execute()) {
            return;
        } catch (IOException e) {
            e.printStackTrace();
            return;
        }
    }

    void postData(String addr, String attempt_id) throws IOException {
        String url = "http://" + addr + ":8080/give_sample";
        RequestBody formBody = new FormBody.Builder()
                .add("att_id", attempt_id)
                .add("cert", certificate)
                .add("signature", signature)
                .add("pers_data", json)
                .build();
        Request request = new Request.Builder()
                .url(url)
                .post(formBody)
                .build();

        try (Response response = client.newCall(request).execute()) {
            return;
        } catch (IOException e) {
            e.printStackTrace();
            return;
        }
    }

    private String readStream(InputStream is) {
        try {
            ByteArrayOutputStream bo = new ByteArrayOutputStream();
            int i = is.read();
            while (i != -1) {
                bo.write(i);
                i = is.read();
            }
            return bo.toString();
        } catch (IOException e) {
            return "";
        }
    }

    void getData(Tag tag,String challenge) throws IOException {

        String birth,fname,gname,cnp,serie_id,gender;
        //byte[] selectApp = {0x00,  (byte) 0xA4, 0x04, 0x00, (byte) 0xA0, 0x00, 0x00, 0x00, 0x04, 0x10, 0x10, 0x00};
        byte[] selectAid = {0x00, (byte)0xA4, 0x04, 0x00, 0x10,(byte)0xA0, 0x00, 0x00, 0x00, 0x77, 0x01, 0x08, 0x00, 0x07, 0x00, 0x00, (byte)0xFE, 0x00, 0x00, 0x01, 0x00};
        byte[] selectDf = {0x00, (byte)0xA4, 0x00, 0x0C, 0x00};
        byte[] selectAWPApp = {0x00, (byte)0xA4, 0x02, 0x0C, 0x02, (byte)0xAD, (byte)0xF1};
        byte[] success = {(byte)0x90, 0x00};
        byte[] readpart1_1 = {0x00, (byte)0xB0, 0x00, 0x00, 0x00};
        byte[] readpart1_2 = {0x00, (byte)0xB0, 0x00, (byte)0xE7, 0x19};
        byte[] readpart2_1 = {0x00, (byte)0xB0, 0x01, 0x00, 0x00};
        byte[] readpart2_2 = {0x00, (byte)0xB0, 0x01, (byte)0xE7, 0x19};
        byte[] readpart3_1 = {0x00, (byte)0xB0, 0x02, 0x00, 0x00};
        byte[] readpart3_2 = {0x00, (byte)0xB0, 0x02, (byte)0xE7, 0x19};
        byte[] readpart4_1 = {0x00, (byte)0xB0, 0x03, 0x00, 0x00};
        byte[] readpart4_2 = {0x00, (byte)0xB0, 0x03, (byte)0xE7, 0x19};
        byte[] readpart5_1 = {0x00, (byte)0xB0, 0x04, 0x00, 0x00};
        byte[] cert, chunk, verifyPin;
        byte[] verifyPinStart = {0x00, 0x20, 0x00, 0x01, 0x0C};

        verifyPinStart[4] = (byte)pin.length();
        ByteArrayOutputStream out = new ByteArrayOutputStream();
        out.write(verifyPinStart);
        for(int i=0;i<pin.length();i++)
        {
            out.write((byte)pin.charAt(i));
        }
        verifyPin = out.toByteArray();
        byte[] arr_combined = out.toByteArray();
        byte[] selectAWPAid = {0x00, (byte)0xA4, 0x04, 0x0C, 0x0D, (byte)0xE8, 0x28, (byte)0xBD, 0x08, 0x0F, (byte)0xF2, 0x50, 0x4F, 0x54, 0x20, 0x41, 0x57, 0x50};
        byte[] sign_req = {0x00, (byte)0x88, 0x00, 0x00, 0x20, (byte)0xFF, (byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF,(byte)0xFF, 0x00};
        for(int i=5;i<25;i++)
        {
            sign_req[i] = (byte)challenge.charAt(i-5);
        }
        ByteArrayOutputStream outputStream = new ByteArrayOutputStream( );


        byte[] df_500 = {0x00, (byte)0xA4, 0x02, 0x0C, 0x02, 0x50, 0x00};
        byte[] datafile = {0x00, (byte)0xA4, 0x02, 0x0C, 0x02, 0x50, 0x01};
        byte[] read = {0x00, (byte)0xB0, 0x00, 0x00, 0x00};
        byte c[] = outputStream.toByteArray( );
        IsoDep isoDep = IsoDep.get(tag);
        //String outcome="nothing";
        //this.label.append("start");
        if (isoDep != null) {
            //this.label.append("enter");
            try {
                isoDep.connect();
                byte[] result = isoDep.transceive(selectAid);
                if (result != success) {
                    throw new RuntimeException();
                }
                result = isoDep.transceive(selectDf);
                if (result != success) {
                    throw new RuntimeException();
                }
                result = isoDep.transceive(selectAWPApp);
                if (result != success) {
                    throw new RuntimeException();
                }
                result = isoDep.transceive(readpart1_1);
                chunk = Arrays.copyOfRange(result, 0, result.length - 2);
                outputStream.write( chunk );

                result = isoDep.transceive(readpart1_2);
                chunk = Arrays.copyOfRange(result, 0, result.length - 2);
                outputStream.write( chunk );

                result = isoDep.transceive(readpart2_1);
                chunk = Arrays.copyOfRange(result, 0, result.length - 2);
                outputStream.write( chunk );

                result = isoDep.transceive(readpart2_2);
                chunk = Arrays.copyOfRange(result, 0, result.length - 2);
                outputStream.write( chunk );

                result = isoDep.transceive(readpart3_1);
                chunk = Arrays.copyOfRange(result, 0, result.length - 2);
                outputStream.write( chunk );

                result = isoDep.transceive(readpart3_2);
                chunk = Arrays.copyOfRange(result, 0, result.length - 2);
                outputStream.write( chunk );

                result = isoDep.transceive(readpart4_1);
                chunk = Arrays.copyOfRange(result, 0, result.length - 2);
                outputStream.write( chunk );

                result = isoDep.transceive(readpart4_2);
                chunk = Arrays.copyOfRange(result, 0, result.length - 2);
                outputStream.write( chunk );

                result = isoDep.transceive(readpart5_1);
                chunk = Arrays.copyOfRange(result, 0, result.length - 2);
                outputStream.write(chunk);
                cert = outputStream.toByteArray();
                this.label.setText(Arrays.toString(result));
                isoDep.close();
                if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.O) {
                    certificate = Arrays.toString(Base64.getEncoder().encode(cert));
                }

                isoDep.connect();
                result = isoDep.transceive(selectAid);
                if (result != success) {
                    throw new RuntimeException();
                }

                result = isoDep.transceive(verifyPin);
                if (result != success) {
                    throw new RuntimeException();
                }

                result = isoDep.transceive(selectAWPAid);
                if (result != success) {
                    throw new RuntimeException();
                }

                result = isoDep.transceive(sign_req);
                if (result != success) {
                    throw new RuntimeException();
                }
                isoDep.close();

                byte[] signed = chunk = Arrays.copyOfRange(result, 0, result.length - 2);
                signature = Arrays.toString(signed);


                isoDep.connect();
                result = isoDep.transceive(selectAid);
                if (result != success) {
                    throw new RuntimeException();
                }

                result = isoDep.transceive(selectDf);
                if (result != success) {
                    throw new RuntimeException();
                }

                result = isoDep.transceive(df_500);
                if (result != success) {
                    throw new RuntimeException();
                }

                //read fname
                int i=1;
                datafile[6] = (byte)i;
                result = isoDep.transceive(datafile);
                if (result != success) {
                    throw new RuntimeException();
                }
                result = isoDep.transceive(read);
                if (result != success) {
                    throw new RuntimeException();
                }
                byte[] famname = Arrays.copyOfRange(result, 0, result.length - 2);
                fname = Arrays.toString(famname);

                //read gname
                i=2;
                datafile[6] = (byte)i;
                result = isoDep.transceive(datafile);
                if (result != success) {
                    throw new RuntimeException();
                }
                result = isoDep.transceive(read);
                if (result != success) {
                    throw new RuntimeException();
                }
                byte[] givname = Arrays.copyOfRange(result, 0, result.length - 2);
                gname = Arrays.toString(givname);

                //read gender
                i=3;
                datafile[6] = (byte)i;
                result = isoDep.transceive(datafile);
                if (result != success) {
                    throw new RuntimeException();
                }
                result = isoDep.transceive(read);
                if (result != success) {
                    throw new RuntimeException();
                }
                byte[] gnder = Arrays.copyOfRange(result, 0, result.length - 2);
                gender = Arrays.toString(gnder);

                //read birthdate
                i=5;
                datafile[6] = (byte)i;
                result = isoDep.transceive(datafile);
                if (result != success) {
                    throw new RuntimeException();
                }
                result = isoDep.transceive(read);
                if (result != success) {
                    throw new RuntimeException();
                }
                byte[] brth = Arrays.copyOfRange(result, 0, result.length - 2);
                birth = Arrays.toString(brth);

                //read cnp
                i=6;
                datafile[6] = (byte)i;
                result = isoDep.transceive(datafile);
                if (result != success) {
                    throw new RuntimeException();
                }
                result = isoDep.transceive(read);
                if (result != success) {
                    throw new RuntimeException();
                }
                byte[] cnp_pers = Arrays.copyOfRange(result, 0, result.length - 2);
                cnp = Arrays.toString(cnp_pers);

                //read serie_id
                i=7;
                datafile[6] = (byte)i;
                result = isoDep.transceive(datafile);
                if (result != success) {
                    throw new RuntimeException();
                }
                result = isoDep.transceive(read);
                if (result != success) {
                    throw new RuntimeException();
                }
                byte[] serie = Arrays.copyOfRange(result, 0, result.length - 2);
                serie_id = Arrays.toString(serie);

                json = "{\"fname\":\"" + fname + "\",\"gname\":\"" + gname + "\",\"birth\":\"" +
                        birth + "\",\"gender\":\"" + gender + "\",\"cnp\":\"" + cnp + "\",\"serie_id\":\"" +
                        serie_id + "\"}";

                isoDep.close();
                postData(name,att_id);

            } catch (IOException e) {
                throw new RuntimeException(e);
            }
        }
        //this.label.append(outcome);
    }

    @Override
    public void onTagDiscovered(Tag tag) {
        //this.label.append("scan");
        try {
            getData(tag, challenge);
        } catch (IOException e) {
            throw new RuntimeException(e);
        }
    }
}